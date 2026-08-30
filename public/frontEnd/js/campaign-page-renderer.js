/** Runtime behavior for published visual campaign pages. */
(function () {
    'use strict';

    const root = document.getElementById('campaign-builder-storefront');
    if (!root) return;

    const products = Array.isArray(window._campaignProducts) ? window._campaignProducts : [];
    const campaign = window._campaignData || {};
    const loading = document.getElementById('cpb-store-loading');
    const toastElement = document.getElementById('cpb-store-toast');
    let selectedProductId = String(products[0]?.id || '');
    let requestInProgress = false;
    let variantSelectionSynced = true;
    let toastTimer = null;

    /* পপআপের চলমান অবস্থা: প্রোডাক্ট, নির্বাচিত সাইজ/কালার, পরিমাণ, লাইভ প্রাইস/স্টক */
    const modal = document.getElementById('cpb-modal');
    const state = { product: null, size: null, color: null, qty: 1, price: 0, stock: null };
    /* কনফার্ম হওয়া ভ্যারিয়েন্ট (order form guard এই তথ্যটাই দেখে) */
    let confirmedSelection = null;

    function cloneTemplate(id) {
        return document.getElementById(id)?.content.cloneNode(true) || document.createDocumentFragment();
    }

    function mountDynamicContent() {
        root.querySelectorAll('[data-cpb-dynamic="products"]').forEach(container => {
            const label = container.dataset.buttonLabel || 'অর্ডার করুন';
            container.replaceChildren(cloneTemplate('cpb-live-products-template'));
            container.querySelectorAll('[data-product-button-label]').forEach(node => { node.textContent = label; });
        });

        root.querySelectorAll('[data-cpb-dynamic="reviews"]').forEach(container => {
            container.replaceChildren(cloneTemplate('cpb-live-reviews-template'));
        });

        let checkoutTargets = Array.from(root.querySelectorAll('[data-cpb-dynamic="checkout"]'));
        if (!checkoutTargets.length) {
            const holder = document.createElement('div');
            holder.setAttribute('data-cpb-dynamic', 'checkout');
            root.appendChild(holder);
            checkoutTargets = [holder];
        }
        checkoutTargets.forEach((container, index) => {
            if (index === 0) {
                container.id = 'order_form';
                container.replaceChildren(cloneTemplate('cpb-live-checkout-template'));
            } else {
                container.innerHTML = '<div class="cpb-empty-dynamic">Checkout form উপরে দেখানো হয়েছে। <a href="#order_form">অর্ডার ফর্মে যান</a></div>';
            }
        });

        // গ্যালারি ও Size Chart — builder-এ ব্লক থাকলে সেখানে, নাহলে পেজের শেষে
        mountOptionalSection('gallery', 'cpb-live-gallery-template');
        mountOptionalSection('sizechart', 'cpb-live-sizechart-template');

        mountSelectedVariantSummary();
        root.querySelectorAll('[data-cpb-youtube]').forEach(mountYoutube);
        initializeCountdowns();
        hideLegacyVariantPicker();
        // ডেলিভারি এখন রেডিও কার্ড। প্রথম কার্ড ডিফল্ট সিলেক্টেড এবং কন্ট্রোলার
        // ইতিমধ্যে সেশনে সেই চার্জ বসিয়ে রেখেছে, তাই তখন অপ্রয়োজনীয় রিকোয়েস্ট পাঠাই না।
        const zoneInputs = Array.from(root.querySelectorAll('input[name="area"]'));
        const checkedZone = root.querySelector('input[name="area"]:checked');
        if (checkedZone && zoneInputs.length > 1 && checkedZone !== zoneInputs[0]) updateShipping(checkedZone.value);

        const stickyButton = document.getElementById('cpb-sticky-order');
        if (stickyButton) stickyButton.hidden = checkoutTargets.length === 0;
        if (checkoutTargets.length && root.querySelector('.cpb-form-errors')) {
            setTimeout(() => checkoutTargets[0].scrollIntoView({ behavior: 'smooth', block: 'start' }), 150);
        }
    }

    /**
     * ঐচ্ছিক সেকশন mount করে।
     *
     * Visual builder-এ সংশ্লিষ্ট ব্লক থাকলে page_html-এ data-cpb-dynamic="<marker>"
     * মার্কার থাকে — তখন সেখানেই বসে, ফলে অ্যাডমিন জায়গা ঠিক করতে পারেন। মার্কার না
     * থাকলে (পুরনো ক্যাম্পেইন বা premium টেমপ্লেট) পেজের শেষে append হয়, যাতে
     * ফিচারটা হারিয়ে না যায়। একাধিক মার্কার থাকলে শুধু প্রথমটা ব্যবহার হয়।
     */
    function mountOptionalSection(marker, templateId) {
        const template = document.getElementById(templateId);
        if (!template) return;

        // ডেটা না থাকলে (যেমন গ্যালারিতে মাত্র একটা ছবি, বা প্রোডাক্টে size chart নেই)
        // builder-এর স্ট্যাটিক প্রিভিউ মার্কাপ যেন লাইভ পেজে না দেখায় — মার্কারটা সরিয়ে দিই।
        if (!template.content.children.length) {
            root.querySelectorAll('[data-cpb-dynamic="' + marker + '"]').forEach(node => node.remove());
            return;
        }

        const targets = Array.from(root.querySelectorAll('[data-cpb-dynamic="' + marker + '"]'));
        if (targets.length) {
            targets[0].replaceChildren(template.content.cloneNode(true));
            targets.slice(1).forEach(node => node.remove());
            return;
        }
        root.appendChild(template.content.cloneNode(true));
    }

    function mountYoutube(container) {
        const id = youtubeId(container.dataset.cpbYoutube || '');
        if (!id) return;
        const iframe = document.createElement('iframe');
        iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id);
        iframe.title = 'Campaign video';
        iframe.loading = 'lazy';
        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
        iframe.allowFullscreen = true;
        const frame = document.createElement('div');
        frame.className = 'cpb-video-frame';
        frame.appendChild(iframe);
        container.replaceWith(frame);
    }

    function youtubeId(input) {
        const value = String(input || '').trim();
        if (/^[a-zA-Z0-9_-]{6,20}$/.test(value)) return value;
        try {
            const url = new URL(value);
            if (url.hostname.includes('youtu.be')) return url.pathname.split('/').filter(Boolean)[0] || '';
            if (url.hostname.includes('youtube.com')) return url.searchParams.get('v') || url.pathname.split('/').filter(Boolean).pop() || '';
        } catch (_) {}
        return '';
    }

    function initializeCountdowns() {
        const countdowns = Array.from(root.querySelectorAll('[data-cpb-countdown]'));
        if (!countdowns.length) return;
        const update = function () {
            countdowns.forEach(container => {
                const deadline = new Date(container.dataset.cpbCountdown).getTime();
                if (!Number.isFinite(deadline)) { container.hidden = true; return; }
                const distance = Math.max(0, deadline - Date.now());
                const values = {
                    days: Math.floor(distance / 86400000),
                    hours: Math.floor((distance % 86400000) / 3600000),
                    minutes: Math.floor((distance % 3600000) / 60000),
                    seconds: Math.floor((distance % 60000) / 1000)
                };
                Object.entries(values).forEach(([key, value]) => {
                    const target = container.querySelector('[data-' + key + ']');
                    if (target) target.textContent = String(value).padStart(2, '0');
                });
                container.classList.toggle('is-expired', distance === 0);
            });
        };
        update();
        window.setInterval(update, 1000);
    }

    /**
     * পুরনো dropdown variant picker আর ব্যবহার হয় না — পুরনো builder HTML
     * না ভাঙার জন্য শুধু hidden করে রাখি (select-গুলোর required-ও সরাই,
     * যাতে browser validation অর্ডার ফর্ম আটকে না দেয়)।
     */
    function hideLegacyVariantPicker() {
        const picker = root.querySelector('#cpb-variant-picker');
        if (!picker) return;
        picker.hidden = true;
        picker.querySelectorAll('select').forEach(select => { select.required = false; select.disabled = true; });
    }

    /* নির্বাচিত ভ্যারিয়েন্ট সামারি bar চেকআউট ব্লকের ঠিক উপরে বসাই */
    function mountSelectedVariantSummary() {
        if (root.querySelector('#cpb-selected-variant')) return;
        const template = document.getElementById('cpb-selected-variant-template');
        const anchor = root.querySelector('#cpb-variant-picker') || root.querySelector('.cpb-checkout-columns');
        if (!template || !anchor) return;
        anchor.parentNode.insertBefore(template.content.cloneNode(true), anchor);
    }

    function findProduct(id) {
        return products.find(product => String(product.id) === String(id));
    }

    function productSizes(product) { return (product && product.sizes) || []; }
    function productColors(product) { return (product && product.colors) || []; }
    function productVariants(product) { return (product && product.variants) || []; }
    function hasVariantOptions(product) { return productSizes(product).length > 0 || productColors(product).length > 0; }

    function formatMoney(value) { return Number(value || 0).toLocaleString('en-US'); }
    function byId(id) { return document.getElementById(id); }

    /* ---------- পপআপ খোলা/বন্ধ ---------- */
    function openVariantModal(productId, moveToCheckout) {
        const product = findProduct(productId);
        if (!product) return;
        state.product = product;
        state.size = null;
        state.color = null;
        state.qty = 1;
        state.price = Number(product.price || 0);
        state.stock = product.stock === undefined ? null : product.stock;
        state.moveToCheckout = !!moveToCheckout;

        /* সাইজ/কালার কিছুই না থাকলে পপআপ ছাড়াই সরাসরি কার্টে */
        if (!hasVariantOptions(product)) {
            confirmVariantSelection();
            return;
        }

        if (!modal) { confirmVariantSelection(); return; }

        byId('cpb-mo-img').src = product.image || '';
        byId('cpb-mo-img').alt = product.name || '';
        byId('cpb-mo-name').textContent = product.name || '';

        buildChips('cpb-sizes', 'cpb-size-wrap', productSizes(product), 'size');
        buildChips('cpb-colors', 'cpb-color-wrap', productColors(product), 'color');

        /* একটাই অপশন থাকলে অটো-সিলেক্ট (কম ক্লিক = বেশি কনভার্শন) */
        if (productSizes(product).length === 1) pickChip('size', productSizes(product)[0].id);
        if (productColors(product).length === 1) pickChip('color', productColors(product)[0].id);

        syncVariantState();
        setQty(1);
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeVariantModal() {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    /* ---------- সাইজ/কালার চিপ ---------- */
    function buildChips(boxId, wrapId, list, type) {
        const box = byId(boxId);
        const wrap = byId(wrapId);
        if (!box || !wrap) return;
        box.innerHTML = '';
        box.classList.remove('is-error');
        if (!list.length) { wrap.hidden = true; return; }
        wrap.hidden = false;
        list.forEach(option => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'cpb-chip';
            chip.dataset.id = String(option.id);
            let stockNote = '';
            if (type === 'size' && option.has_stock) {
                stockNote = '<small>' + (Number(option.stock) > 0 ? escapeHtml(option.stock + ' টি আছে') : 'স্টক শেষ') + '</small>';
                if (Number(option.stock) <= 0) chip.classList.add('is-out');
            }
            const dot = (type === 'color' && option.hex) ? '<span class="cpb-dot" style="background:' + escapeHtml(option.hex) + '"></span>' : '';
            chip.innerHTML = dot + escapeHtml(option.name || '') + stockNote;
            chip.addEventListener('click', () => { if (!chip.classList.contains('is-out')) pickChip(type, option.id); });
            box.appendChild(chip);
        });
    }

    function pickChip(type, id) {
        const box = byId(type === 'size' ? 'cpb-sizes' : 'cpb-colors');
        if (!box) return;
        Array.from(box.children).forEach(chip => chip.classList.toggle('is-active', String(chip.dataset.id) === String(id)));
        box.classList.remove('is-error');
        state[type] = String(id);
        syncVariantState();
        setQty(state.qty);
    }

    /* ---------- ভ্যারিয়েন্ট মিলিয়ে availability / price / stock লাইভ আপডেট ---------- */
    function markAvailability(boxId, isAvailable) {
        const box = byId(boxId);
        if (!box) return;
        Array.from(box.children).forEach(chip => {
            const ok = isAvailable(chip.dataset.id);
            chip.classList.toggle('is-out', !ok);
            if (!ok) chip.classList.remove('is-active');
        });
    }

    function variantMatches(variant, sizeId, colorId) {
        const sizeOk = sizeId == null || variant.size_id == null || String(variant.size_id) === String(sizeId);
        const colorOk = colorId == null || variant.color_id == null || String(variant.color_id) === String(colorId);
        return sizeOk && colorOk;
    }

    function syncVariantState() {
        const product = state.product;
        if (!product) return;
        const variants = productVariants(product);

        if (variants.length) {
            markAvailability('cpb-sizes', id => variants.some(variant =>
                String(variant.size_id) === String(id) &&
                (state.color == null || variant.color_id == null || String(variant.color_id) === String(state.color)) &&
                (variant.stock === null || Number(variant.stock) > 0)
            ));
            markAvailability('cpb-colors', id => variants.some(variant =>
                String(variant.color_id) === String(id) &&
                (state.size == null || variant.size_id == null || String(variant.size_id) === String(state.size)) &&
                (variant.stock === null || Number(variant.stock) > 0)
            ));

            const matched = variants.filter(variant => variantMatches(variant, state.size, state.color));
            const chosen = (!productSizes(product).length || state.size != null) && (!productColors(product).length || state.color != null);
            if (chosen && matched.length) {
                state.price = Number(matched[0].price) > 0 ? Number(matched[0].price) : Number(product.price || 0);
                const rows = matched.filter(variant => variant.stock !== null);
                state.stock = rows.length
                    ? ((state.color != null || rows.length === 1) ? Number(rows[0].stock) : rows.reduce((sum, variant) => sum + Number(variant.stock), 0))
                    : (product.stock === undefined ? null : product.stock);
            } else {
                state.price = Number(product.price || 0);
                state.stock = product.stock === undefined ? null : product.stock;
            }
        }

        const priceEl = byId('cpb-mo-price');
        const oldEl = byId('cpb-mo-old');
        const saveEl = byId('cpb-mo-save');
        const stockEl = byId('cpb-mo-stock');
        if (priceEl) priceEl.textContent = '৳ ' + formatMoney(state.price);
        if (oldEl && saveEl) {
            const oldPrice = Number(product.old_price || 0);
            if (oldPrice > state.price) {
                oldEl.textContent = '৳ ' + formatMoney(oldPrice);
                saveEl.textContent = 'সাশ্রয় ৳ ' + formatMoney(oldPrice - state.price);
                oldEl.hidden = false; saveEl.hidden = false;
            } else { oldEl.hidden = true; saveEl.hidden = true; }
        }
        if (stockEl) {
            if (state.stock !== null && state.stock !== undefined && Number(state.stock) <= 0) {
                stockEl.textContent = '❌ এই ভ্যারিয়েন্টটি স্টকে নেই';
                stockEl.style.color = '#dc2626';
            } else if (state.stock !== null && state.stock !== undefined && Number(state.stock) > 0 && Number(state.stock) <= 20) {
                stockEl.textContent = '🔥 তাড়াতাড়ি করুন! মাত্র ' + state.stock + ' টি বাকি';
                stockEl.style.color = '#ea580c';
            } else {
                stockEl.textContent = '✅ স্টকে আছে';
                stockEl.style.color = '#12a150';
            }
        }
    }

    /* ---------- পরিমাণ ---------- */
    function setQty(value) {
        const max = (state.stock !== null && state.stock !== undefined && Number(state.stock) > 0) ? Number(state.stock) : 99;
        state.qty = Math.max(1, Math.min(Number(value) || 1, max));
        const box = byId('cpb-qty-box');
        const total = byId('cpb-mo-total');
        if (box) box.value = state.qty;
        if (total) total.textContent = '৳ ' + formatMoney(state.price * state.qty);
    }

    function shakeChips(boxId) {
        const box = byId(boxId);
        if (!box) return;
        box.classList.add('is-error', 'cpb-shake');
        setTimeout(() => box.classList.remove('cpb-shake'), 420);
        box.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    /* ---------- কনফার্ম → cart.changeProduct (size/color/qty সহ) ---------- */
    async function confirmVariantSelection() {
        const product = state.product;
        if (!product || requestInProgress) return;
        if (productSizes(product).length && !state.size) { shakeChips('cpb-sizes'); return; }
        if (productColors(product).length && !state.color) { shakeChips('cpb-colors'); return; }
        if (state.stock !== null && state.stock !== undefined && Number(state.stock) <= 0) {
            showToast('এই ভ্যারিয়েন্টটি স্টকে নেই');
            return;
        }

        const confirmButton = byId('cpb-mo-confirm');
        if (confirmButton) confirmButton.disabled = true;
        setBusy(true);
        try {
            const html = await requestHtml(root.dataset.changeProductUrl, {
                id: product.id,
                product_size: state.size || '',
                product_color: state.color || '',
                qty: state.qty
            });
            selectedProductId = String(product.id);
            confirmedSelection = {
                productId: String(product.id),
                size: productSizes(product).find(item => String(item.id) === String(state.size)) || null,
                color: productColors(product).find(item => String(item.id) === String(state.color)) || null,
                qty: state.qty,
                price: state.price
            };
            variantSelectionSynced = true;
            replaceCart(html);
            updateSelectedCards(product.id);
            showSelectedVariant(product);
            trackAddToCart(product.id, state.price, state.qty);
            closeVariantModal();
            showToast('✔ কার্টে যোগ হয়েছে — এখন ডেলিভারি তথ্য দিন', true);
            document.dispatchEvent(new CustomEvent('campaign:product-selected', { detail: { product, size: state.size, color: state.color, qty: state.qty } }));
            if (state.moveToCheckout) root.querySelector('#order_form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (error) {
            showToast(error.message || 'কার্ট আপডেট করা যায়নি। আবার চেষ্টা করুন।');
        } finally {
            if (confirmButton) confirmButton.disabled = false;
            setBusy(false);
        }
    }

    function showSelectedVariant(product) {
        const box = root.querySelector('#cpb-selected-variant');
        const text = root.querySelector('#cpb-selected-variant-text');
        if (!box || !text) return;
        const parts = [];
        if (state.size) {
            const size = productSizes(product).find(item => String(item.id) === String(state.size));
            if (size) parts.push('সাইজ: ' + size.name);
        }
        if (state.color) {
            const color = productColors(product).find(item => String(item.id) === String(state.color));
            if (color) parts.push('কালার: ' + color.name);
        }
        parts.push('পরিমাণ: ' + state.qty + ' টি');
        text.textContent = '✓ ' + String(product.name || '').substring(0, 30) + ' — ' + parts.join(' | ');
        box.hidden = false;
        box.dataset.productId = String(product.id);
    }

    /**
     * প্রোডাক্ট সিলেক্ট: ভ্যারিয়েন্ট থাকলে পপআপ, নাহলে সরাসরি কার্টে।
     */
    function highlightCard(productId) {
        root.querySelectorAll('[data-cpb-card]').forEach((card) => {
            const on = String(card.dataset.productCard) === String(productId);
            card.classList.toggle('is-selected', on);
            const radio = card.querySelector('.ms-radio-input');
            if (radio) radio.checked = on;
        });
    }

    function selectProduct(productId, moveToCheckout) {
        productId = String(productId || '');
        if (!productId || requestInProgress) return;
        highlightCard(productId);
        const card = cardOf(productId);
        const product = findProduct(productId);
        if (card && product && !cardSelectionComplete(card, product)) {
            return;
        }
        if (applyCardSelection(productId, moveToCheckout)) return;
        openVariantModal(productId, moveToCheckout);
    }

    async function mutateCart(url, rowId) {
        if (!url || !rowId || requestInProgress) return;
        setBusy(true);
        try { replaceCart(await requestHtml(url, { id: rowId })); }
        catch (error) { showToast(error.message || 'কার্ট আপডেট করা যায়নি।'); }
        finally { setBusy(false); }
    }

    async function updateShipping(areaId) {
        if (!areaId || requestInProgress) return;
        setBusy(true);
        try { replaceCart(await requestHtml(root.dataset.shippingUrl, { id: areaId })); }
        catch (error) { showToast(error.message || 'ডেলিভারি চার্জ আপডেট করা যায়নি।'); }
        finally { setBusy(false); }
    }

    async function requestHtml(url, params) {
        const requestUrl = new URL(url, window.location.origin);
        requestUrl.searchParams.set('campaign', '1');
        Object.entries(params || {}).forEach(([key, value]) => requestUrl.searchParams.set(key, value));
        const response = await fetch(requestUrl.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html', 'X-Campaign-Page': '1' }, credentials: 'same-origin' });
        const contentType = response.headers.get('content-type') || '';
        if (!response.ok) {
            if (contentType.includes('application/json')) {
                const data = await response.json().catch(() => ({}));
                throw new Error(data.message || 'Request failed.');
            }
            throw new Error('Request failed (' + response.status + ').');
        }
        return response.text();
    }

    function replaceCart(html) {
        root.querySelectorAll('.cartlist').forEach(container => { container.innerHTML = html; });
    }

    function updateSelectedCards(productId) {
        root.querySelectorAll('[data-product-card], .cpb-compact-product').forEach(card => {
            const id = card.dataset.productCard || card.dataset.selectProduct;
            card.classList.toggle('is-selected', String(id) === String(productId));
        });
    }

    function setBusy(value) {
        requestInProgress = value;
        if (loading) loading.hidden = true;
        document.body.classList.remove('cpb-store-busy');
    }

    function showToast(message, isSuccess) {
        if (!toastElement) return;
        window.clearTimeout(toastTimer);
        toastElement.textContent = message;
        toastElement.style.background = isSuccess ? '#12a150' : '#b91c1c';
        toastElement.classList.add('is-visible');
        toastTimer = window.setTimeout(() => toastElement.classList.remove('is-visible'), 4200);
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, character => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[character]));
    }

    function trackAddToCart(productId, price, qty) {
        const product = findProduct(productId) || {};
        const unitPrice = Number(price !== undefined ? price : (product.price || 0));
        const quantity = Math.max(1, Number(qty || 1));
        const item = { item_id: String(productId), item_name: product.name || '', price: unitPrice, quantity };
        const value = unitPrice * quantity;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({ event: 'add_to_cart', ecommerce: { currency: 'BDT', value, items: [item] } });
        if (typeof window.fbq === 'function') {
            window.fbq('track', 'AddToCart', { content_ids: [item.item_id], content_name: item.item_name, content_type: 'product', value, currency: 'BDT' }, { eventID: 'atc_' + item.item_id + '_' + Math.floor(Date.now() / 1000) });
        }
        if (typeof window.ttq !== 'undefined' && typeof window.ttq.track === 'function') {
            window.ttq.track('AddToCart', { content_id: item.item_id, content_name: item.item_name, content_type: 'product', value, currency: 'BDT', quantity });
        }
    }

    function trackCheckout() {
        const subtotalNode = root.querySelector('#net_total strong');
        const subtotal = Number.parseFloat(String(subtotalNode?.textContent || '0').replace(/[^0-9.]/g, '')) || 0;
        const selectedProduct = findProduct(selectedProductId) || products[0] || {};
        const items = [{ item_id: String(selectedProduct.id || ''), item_name: selectedProduct.name || '', price: Number(selectedProduct.price || 0), index: 0, quantity: 1 }];
        const ids = items[0].item_id ? [items[0].item_id] : [];
        const timestamp = Math.floor(Date.now() / 1000);
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({ event: 'begin_checkout', ecommerce: { currency: 'BDT', value: subtotal, items } });
        if (typeof window.fbq === 'function') {
            window.fbq('track', 'InitiateCheckout', { content_ids: ids, content_type: 'product', value: subtotal, currency: 'BDT', num_items: ids.length }, { eventID: 'ic_camp' + campaign.id + '_' + timestamp });
            window.fbq('track', 'Lead', { value: subtotal, currency: 'BDT', content_name: campaign.name || '' }, { eventID: 'lead_camp' + campaign.id + '_' + timestamp });
        }
        if (typeof window.ttq !== 'undefined' && typeof window.ttq.track === 'function') {
            window.ttq.track('InitiateCheckout', { content_ids: ids, content_type: 'product', value: subtotal, currency: 'BDT', quantity: ids.length });
        }
    }

    function trackOrderClick() {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'click_order_now_button', campaign_id: campaign.id || '', campaign_name: campaign.name || '' });
    }

    /* ============================================================
       ইনলাইন প্রোডাক্ট কার্ড — সাইজ/কালার পিল + পরিমাণ + প্যাকেজ দাম
       পপআপের বদলে কার্ডের ভিতরেই সিলেকশন, তারপর সরাসরি কার্টে যায়।
       ভ্যারিয়েন্ট availability/price লজিক পপআপের সাথেই শেয়ার করা।
       ============================================================ */
    function cardOf(productId) {
        return root.querySelector('[data-cpb-card][data-product-card="' + productId + '"]');
    }

    function cardSelection(card) {
        return {
            size: card.querySelector('[data-cpb-size].is-active')?.dataset.cpbSize || null,
            color: card.querySelector('[data-cpb-color].is-active')?.dataset.cpbColor || null,
            qty: Number(card.querySelector('[data-cpb-qty-input]')?.value || 1)
        };
    }

    function shakeCard(box) {
        if (!box) return;
        box.classList.add('is-error', 'cpb-shake');
        setTimeout(() => box.classList.remove('cpb-shake'), 420);
        box.scrollIntoView({ block: 'center', behavior: 'smooth' });
    }

    /** কার্ডের বর্তমান সিলেকশন অনুযায়ী দাম/স্টক/মোট আপডেট করে। */
    function syncCard(card) {
        const product = findProduct(card.dataset.productCard);
        if (!product) return;
        const selection = cardSelection(card);
        const variants = productVariants(product);
        const sizeBox = card.querySelector('[data-cpb-size-box]');
        const colorBox = card.querySelector('[data-cpb-color-box]');

        let unit = Number(card.dataset.unitPrice || product.price || 0);
        let stock = product.stock === undefined ? null : Number(product.stock);

        if (variants.length) {
            if (sizeBox) sizeBox.querySelectorAll('[data-cpb-size]').forEach(pill => {
                const ok = variants.some(variant => String(variant.size_id) === String(pill.dataset.cpbSize)
                    && (selection.color == null || variant.color_id == null || String(variant.color_id) === String(selection.color))
                    && (variant.stock === null || Number(variant.stock) > 0));
                pill.classList.toggle('is-out', !ok);
            });
            if (colorBox) colorBox.querySelectorAll('[data-cpb-color]').forEach(pill => {
                const ok = variants.some(variant => String(variant.color_id) === String(pill.dataset.cpbColor)
                    && (selection.size == null || variant.size_id == null || String(variant.size_id) === String(selection.size))
                    && (variant.stock === null || Number(variant.stock) > 0));
                pill.classList.toggle('is-out', !ok);
            });

            const matched = variants.filter(variant => variantMatches(variant, selection.size, selection.color));
            const chosen = (!productSizes(product).length || selection.size) && (!productColors(product).length || selection.color);
            if (chosen && matched.length) {
                unit = Number(matched[0].price) > 0 ? Number(matched[0].price) : unit;
                const rows = matched.filter(variant => variant.stock !== null);
                stock = rows.length
                    ? ((selection.color != null || rows.length === 1) ? Number(rows[0].stock) : rows.reduce((sum, variant) => sum + Number(variant.stock), 0))
                    : stock;
            }
        }

        // প্যাকেজ দাম: পরিমাণ টিয়ারে পৌঁছালে প্রতি-পিস দাম কমে যায়
        const tier = (product.tiers || []).find(row => selection.qty >= Number(row.min) && (row.max === null || selection.qty <= Number(row.max)));
        const lineTotal = tier ? Math.round(Number(tier.unit)) * selection.qty : unit * selection.qty;

        card.dataset.unitPrice = String(unit);
        card.dataset.maxQty = String((stock !== null && stock > 0) ? stock : 99);
        card.dataset.lineTotal = String(lineTotal);

        const priceEl = card.querySelector('[data-cpb-price]');
        if (priceEl) priceEl.textContent = '৳' + formatMoney(unit);

        const totalEl = card.querySelector('[data-cpb-total]');
        if (totalEl) {
            if (selection.qty > 1) {
                totalEl.hidden = false;
                totalEl.textContent = (tier ? '✓ প্যাকেজ দাম: ' : 'মোট: ') + '৳' + formatMoney(lineTotal);
            } else {
                totalEl.hidden = true;
            }
        }
    }

    function setCardQty(card, next) {
        const input = card.querySelector('[data-cpb-qty-input]');
        if (!input) return;
        const max = Number(card.dataset.maxQty || 99);
        input.value = String(Math.max(1, Math.min(Number(next) || 1, max)));
        syncCard(card);
    }

    /** কার্ডের সিলেকশন যাচাই করে শেয়ার করা state-এ বসায়, তারপর কার্টে পাঠায়। */
    function applyCardSelection(productId, moveToCheckout) {
        const card = cardOf(productId);
        if (!card) return false;
        const product = findProduct(productId);
        if (!product) return false;

        const selection = cardSelection(card);
        if (productSizes(product).length && !selection.size) {
            shakeCard(card.querySelector('[data-cpb-size-box]'));
            showToast('সাইজ বেছে নিন');
            return true;
        }
        if (productColors(product).length && !selection.color) {
            shakeCard(card.querySelector('[data-cpb-color-box]'));
            showToast('কালার বেছে নিন');
            return true;
        }

        state.product = product;
        state.size = selection.size;
        state.color = selection.color;
        state.qty = selection.qty;
        state.price = Number(card.dataset.unitPrice || product.price || 0);
        const maxQty = Number(card.dataset.maxQty || 0);
        state.stock = maxQty > 0 ? maxQty : null;
        state.moveToCheckout = !!moveToCheckout;

        confirmVariantSelection();
        return true;
    }

    /** কার্ডে প্রয়োজনীয় সাইজ/কালার বাছা হয়েছে কিনা (স্টক না থাকা পিল বাদে)। */
    function cardSelectionComplete(card, product) {
        const selection = cardSelection(card);
        if (productSizes(product).length && !selection.size) return false;
        if (productColors(product).length && !selection.color) return false;
        return true;
    }

    function initProductCards() {
        root.querySelectorAll('[data-cpb-card]').forEach(card => {
            // একটাই অপশন থাকলে অটো-সিলেক্ট (কম ক্লিক = বেশি কনভার্শন)
            const sizes = card.querySelectorAll('[data-cpb-size]:not(.is-out)');
            const colors = card.querySelectorAll('[data-cpb-color]');
            if (sizes.length === 1) sizes[0].classList.add('is-active');
            if (colors.length === 1) colors[0].classList.add('is-active');
            syncCard(card);
        });
    }

    /* ---------- ছবি বড় করে দেখা (lightbox) ---------- */
    let zoomElement = null;

    function openZoom(src, alt) {
        if (!src) return;
        if (!zoomElement) {
            zoomElement = document.createElement('div');
            zoomElement.className = 'cpb-lightbox';
            zoomElement.setAttribute('role', 'dialog');
            zoomElement.setAttribute('aria-modal', 'true');
            zoomElement.innerHTML = '<button type="button" class="cpb-lightbox-close" aria-label="বন্ধ করুন">×</button><img alt="">';
            document.body.appendChild(zoomElement);
            zoomElement.addEventListener('click', event => {
                if (event.target === zoomElement || event.target.closest('.cpb-lightbox-close')) closeZoom();
            });
        }
        zoomElement.querySelector('img').src = src;
        zoomElement.querySelector('img').alt = alt || '';
        zoomElement.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeZoom() {
        if (!zoomElement) return;
        zoomElement.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    /* ---------- Size Chart কল্যাপ্সিবল টেবিল ---------- */
    function initSizeChart() {
        const toggle = root.querySelector('[data-cpb-sizechart-toggle]');
        const panel = root.querySelector('[data-cpb-sizechart-panel]');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', () => {
            const willOpen = panel.hidden;
            panel.hidden = !willOpen;
            toggle.classList.toggle('is-open', willOpen);
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    }

    /* ---------- স্টিকি প্যাকেজ আপসেল বার ---------- */
    function initUpsellBar() {
        const bar = root.querySelector('[data-cpb-upsell]');
        if (!bar) return;
        const key = 'cpb_upsell_dismissed_' + (campaign.id || 'x');
        let dismissed = false;
        try { dismissed = window.localStorage.getItem(key) === '1'; } catch (error) { dismissed = false; }
        if (dismissed) { bar.remove(); return; }

        bar.querySelector('[data-cpb-upsell-close]')?.addEventListener('click', () => {
            bar.classList.add('is-hidden');
            try { window.localStorage.setItem(key, '1'); } catch (error) { /* storage বন্ধ থাকলে উপেক্ষা */ }
        });
        updateUpsellBar();
    }

    function updateUpsellBar() {
        const bar = root.querySelector('[data-cpb-upsell]');
        if (!bar) return;
        const product = findProduct(selectedProductId) || products[0];
        const tiers = product?.tiers || [];
        const qty = Number(confirmedSelection?.qty || 1);
        const next = tiers.find(row => Number(row.min) > qty);
        if (!next) { bar.classList.add('is-hidden'); return; }
        bar.classList.remove('is-hidden');
        const need = bar.querySelector('[data-cpb-upsell-need]');
        const price = bar.querySelector('[data-cpb-upsell-price]');
        if (need) need.textContent = String(Number(next.min) - qty);
        if (price) price.textContent = '৳' + formatMoney(next.unit);
    }

    function bindEvents() {
        document.addEventListener('click', event => {
            const zoomTrigger = event.target.closest('[data-cpb-zoom]');
            if (zoomTrigger) { event.preventDefault(); openZoom(zoomTrigger.dataset.zoomSrc, zoomTrigger.dataset.zoomAlt); return; }

            const sizePill = event.target.closest('[data-cpb-size]');
            if (sizePill) {
                event.preventDefault();
                if (sizePill.classList.contains('is-out')) { showToast('এই সাইজটি স্টকে নেই'); return; }
                const sizeCard = sizePill.closest('[data-cpb-card]');
                sizeCard.querySelectorAll('[data-cpb-size]').forEach(pill => pill.classList.toggle('is-active', pill === sizePill));
                syncCard(sizeCard);
                const pid = sizeCard.dataset.productCard;
                const product = findProduct(pid);
                if (product && cardSelectionComplete(sizeCard, product)) selectProduct(pid, false);
                return;
            }

            const colorPill = event.target.closest('[data-cpb-color]');
            if (colorPill) {
                event.preventDefault();
                const colorCard = colorPill.closest('[data-cpb-card]');
                colorCard.querySelectorAll('[data-cpb-color]').forEach(pill => pill.classList.toggle('is-active', pill === colorPill));
                syncCard(colorCard);
                const pid = colorCard.dataset.productCard;
                const product = findProduct(pid);
                if (product && cardSelectionComplete(colorCard, product)) selectProduct(pid, false);
                return;
            }

            const cardQtyButton = event.target.closest('[data-cpb-qty-dec], [data-cpb-qty-inc]');
            if (cardQtyButton) {
                event.preventDefault();
                const qtyCard = cardQtyButton.closest('[data-cpb-card]');
                const current = Number(qtyCard.querySelector('[data-cpb-qty-input]')?.value || 1);
                setCardQty(qtyCard, current + (cardQtyButton.hasAttribute('data-cpb-qty-inc') ? 1 : -1));
                return;
            }

            const productButton = event.target.closest('[data-select-product]');
            if (productButton) {
                event.preventDefault();
                if (productButton.hasAttribute('data-order-product')) trackOrderClick();
                selectProduct(productButton.dataset.selectProduct, productButton.hasAttribute('data-order-product'));
                return;
            }

            const increment = event.target.closest('.cart_increment');
            if (increment) { event.preventDefault(); mutateCart(root.dataset.cartIncrementUrl, increment.dataset.id); return; }
            const decrement = event.target.closest('.cart_decrement');
            if (decrement) { event.preventDefault(); mutateCart(root.dataset.cartDecrementUrl, decrement.dataset.id); return; }
            const remove = event.target.closest('.cart_remove');
            if (remove) { event.preventDefault(); mutateCart(root.dataset.cartRemoveUrl, remove.dataset.id); return; }

            const modalClose = event.target.closest('[data-cpb-modal-close]');
            if (modalClose) { event.preventDefault(); closeVariantModal(); return; }

            const qtyButton = event.target.closest('[data-cpb-qty]');
            if (qtyButton) { event.preventDefault(); setQty(state.qty + Number(qtyButton.dataset.cpbQty)); return; }

            const changeVariant = event.target.closest('#cpb-change-variant');
            if (changeVariant) {
                event.preventDefault();
                const id = root.querySelector('#cpb-selected-variant')?.dataset.productId;
                if (id) openVariantModal(id, false);
                return;
            }

            const orderLink = event.target.closest('a[href="#order_form"], .cam_order_now');
            if (orderLink) trackOrderClick();
        });

        document.getElementById('cpb-mo-confirm')?.addEventListener('click', confirmVariantSelection);

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') { closeVariantModal(); closeZoom(); }
        });

        document.addEventListener('change', event => {
            if (event.target.matches('input[name="area"]')) {
                updateShipping(event.target.value);
                root.querySelectorAll('.cpb-zone-card').forEach(card => {
                    card.classList.toggle('is-checked', !!card.querySelector('input:checked'));
                });
            }
        });

        document.addEventListener('submit', event => {
            const form = event.target.closest('[data-cpb-order-form]');
            if (!form) return;

            if (requestInProgress) {
                event.preventDefault();
                showToast('প্রোডাক্ট আপডেট হচ্ছে—এক মুহূর্ত পরে আবার অর্ডার করুন।');
                return;
            }

            /* Variant guard: ভ্যারিয়েন্টওয়ালা প্রোডাক্ট কনফার্ম না করে অর্ডার করা যাবে না */
            const activeProduct = findProduct(selectedProductId) || products[0];
            const needsVariant = hasVariantOptions(activeProduct);
            const isConfirmed = confirmedSelection && String(confirmedSelection.productId) === String(activeProduct?.id);

            if (needsVariant && !isConfirmed) {
                event.preventDefault();

                /* কার্ডেই সাইজ/কালার বাছা থাকলে পপআপ খোলার দরকার নেই — কার্ডের
                   সিলেকশনটাই কার্টে পাঠাই। সার্ভার কার্ট আপডেট না হওয়া পর্যন্ত অর্ডার
                   সাবমিট করা যাবে না, তাই কাস্টমারকে আবার কনফার্ম করতে বলা হয়। */
                const pendingCard = cardOf(activeProduct?.id);
                if (pendingCard && cardSelectionComplete(pendingCard, activeProduct)) {
                    applyCardSelection(activeProduct.id, false);
                    showToast('সাইজ/কালার কার্টে যোগ হচ্ছে — এক মুহূর্ত পরে আবার অর্ডার কনফার্ম করুন।');
                    return;
                }

                showToast('অর্ডারের আগে সাইজ/কালার সিলেক্ট করুন');
                if (pendingCard) {
                    pendingCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    if (productSizes(activeProduct).length) shakeCard(pendingCard.querySelector('[data-cpb-size-box]'));
                } else {
                    openVariantModal(activeProduct.id, false);
                }
                return;
            }

            if (!variantSelectionSynced) {
                event.preventDefault();
                showToast('এই সাইজ ও কালারের সমন্বয়টি নিশ্চিত করা যায়নি। অন্য অপশন বেছে আবার চেষ্টা করুন।');
                if (activeProduct) openVariantModal(activeProduct.id, false);
                return;
            }

            trackCheckout();
            event.preventDefault();
            const submit = form.querySelector('[type="submit"]');
            if (submit) { submit.disabled = true; const span = submit.querySelector('span'); if (span) span.textContent = 'অর্ডার প্রসেস হচ্ছে...'; }
            const body = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-Campaign-Page': '1', 'X-CSRF-TOKEN': csrf() },
                body
            }).then(async (response) => {
                const data = await response.json().catch(() => ({}));
                if (response.ok && (data.redirect || data.success)) {
                    window.location.href = data.redirect || ('/customer/order-success/' + (data.order_id || ''));
                    return;
                }
                showToast(data.message || 'অর্ডার সম্পন্ন হয়নি। তথ্য যাচাই করুন।');
                if (submit) { submit.disabled = false; const span = submit.querySelector('span'); if (span) span.textContent = 'অর্ডার কনফার্ম করুন'; }
            }).catch(() => {
                showToast('নেটওয়ার্ক সমস্যা। আবার চেষ্টা করুন।');
                if (submit) { submit.disabled = false; }
            });
        });

        document.getElementById('cpb-sticky-order')?.addEventListener('click', () => {
            trackOrderClick();
            root.querySelector('#order_form')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    /* ============================================================
       Abandoned cart capture (ইনকমপ্লিট অর্ডার)
       ক্যাম্পেইন ল্যান্ডিং পেজই সবচেয়ে বেশি ট্রাফিক পায়, অথচ এতদিন এখান থেকে
       কোনো লিড সেভ হতো না। কাস্টমার নাম/ফোন/ঠিকানা লিখলে (অর্ডার সাবমিট না
       করলেও) সেটি /incomplete-order/store এ সেভ হয়, যাতে ফলো-আপ কল করা যায়।
       ============================================================ */
    function initAbandonedCart() {
        const endpoint = root.dataset.incompleteOrderUrl;
        if (!endpoint) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const nameField = document.getElementById('cpb-name');
        const phoneField = document.getElementById('cpb-phone');
        const addressField = document.getElementById('cpb-address');
        if (!phoneField) return;

        let debounceTimer = null;
        let lastPayloadKey = '';
        let orderPlaced = false;

        function selectionSnapshot() {
            const product = findProduct(confirmedSelection?.productId || selectedProductId) || products[0];
            if (!product) return null;

            const qty = Math.max(1, Number(confirmedSelection?.qty || 1));
            const price = Number(confirmedSelection?.price || product.price || 0);
            const parts = [];
            if (confirmedSelection?.size?.name) parts.push('Size: ' + confirmedSelection.size.name);
            if (confirmedSelection?.color?.name) parts.push('Color: ' + confirmedSelection.color.name);

            return {
                id: product.id,
                name: parts.length ? product.name + ' (' + parts.join(', ') + ')' : product.name,
                qty: qty,
                price: price,
                image: product.image || '',
                link: window.location.href
            };
        }

        function buildPayload() {
            const phone = (phoneField.value || '').replace(/[^0-9+]/g, '');
            /* ফোন নম্বর ছাড়া লিডের কোনো মানে নেই — ফলো-আপ করা যাবে না */
            if (phone.length < 11) return null;

            const item = selectionSnapshot();
            if (!item) return null;

            return {
                name: (nameField?.value || '').trim(),
                phone: phone,
                address: (addressField?.value || '').trim(),
                items: [item],
                product_image: item.image,
                product_link: item.link,
                total_amount: Number((item.price * item.qty).toFixed(2)),
                source: 'campaign_' + String(campaign.page_type || 'builder')
            };
        }

        function send(immediate) {
            if (orderPlaced) return;

            const payload = buildPayload();
            if (!payload) return;

            /* একই তথ্য বারবার পাঠানো হবে না */
            const key = JSON.stringify(payload);
            if (key === lastPayloadKey) return;
            lastPayloadKey = key;

            try {
                fetch(endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: Boolean(immediate),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: key
                }).then(response => {
                    if (!response.ok) lastPayloadKey = '';
                }).catch(() => { lastPayloadKey = ''; });
            } catch (_) {
                lastPayloadKey = '';
                /* ক্যাপচার ব্যর্থ হলে চেকআউট কখনোই ব্লক হবে না */
            }
        }

        function schedule() {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(() => send(false), 2000);
        }

        [nameField, phoneField, addressField].forEach(field => {
            field?.addEventListener('input', schedule);
            field?.addEventListener('change', schedule);
        });

        /* সাইজ/কালার কনফার্ম হওয়ার সাথে সাথে স্ন্যাপশট আপডেট */
        document.addEventListener('campaign:product-selected', schedule);

        /* পেজ ছেড়ে যাওয়ার আগে শেষ চেষ্টা */
        document.addEventListener('visibilitychange', () => {
            if (document.visibilityState === 'hidden') {
                window.clearTimeout(debounceTimer);
                send(true);
            }
        });

        /* অর্ডার সাবমিট হলে আর ইনকমপ্লিট হিসেবে সেভ হবে না
           (order_save() নিজেই পুরনো রেকর্ড ডিলিট করে) */
        document.addEventListener('submit', event => {
            if (event.target.closest('[data-cpb-order-form]') && !event.defaultPrevented) {
                orderPlaced = true;
                window.clearTimeout(debounceTimer);
            }
        });
    }

    /* ============================================================
       Coupon + Order bump (কনভার্সন)
       ক্যাম্পেইন পেজ কখনো রিলোড হয় না, তাই দুটোই fetch() দিয়ে চলে এবং
       সার্ভার রিফ্রেশ করা কার্ট HTML ফেরত পাঠায়।
       ============================================================ */
    function csrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * কুপন/বাম্প — দুটোরই POST একই আকৃতির JSON ফেরত দেয়:
     * { ok, message, cart_html }
     */
    async function postConversionAction(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-Campaign-Page': '1',
                'X-CSRF-TOKEN': csrf()
            },
            credentials: 'same-origin',
            body: JSON.stringify(body || {})
        });

        const data = await response.json().catch(() => ({}));
        if (typeof data.cart_html === 'string') replaceCart(data.cart_html);

        return { ok: response.ok && data.ok !== false, message: data.message || '' };
    }

    function initCoupon() {
        const wrapper = root.querySelector('[data-cpb-coupon]');
        if (!wrapper) return;

        const message = wrapper.querySelector('[data-cpb-coupon-msg]');

        function say(text, isSuccess) {
            if (!message) return;
            message.textContent = text;
            message.hidden = !text;
            message.classList.toggle('is-error', !isSuccess);
            message.classList.toggle('is-success', !!isSuccess);
        }

        wrapper.addEventListener('click', async event => {
            const applyButton = event.target.closest('[data-cpb-coupon-apply]');
            const removeButton = event.target.closest('[data-cpb-coupon-remove]');
            if (!applyButton && !removeButton) return;

            event.preventDefault();
            if (requestInProgress) return;

            const button = applyButton || removeButton;
            button.disabled = true;
            setBusy(true);

            try {
                let result;

                if (applyButton) {
                    const input = wrapper.querySelector('#cpb-coupon-code');
                    const code = (input?.value || '').trim();

                    if (!code) {
                        say('কুপন কোডটি লিখুন।', false);
                        return;
                    }

                    /* কুপনের প্রতি-কাস্টমার সীমা ফোন নম্বর দিয়ে গোনা হয়, তাই
                       চেকআউট ফর্মে নম্বর লেখা থাকলে সেটিও পাঠাই। */
                    const phoneField = document.querySelector('#cpb-phone, [name="phone"]');
                    const phone = (phoneField?.value || '').trim();

                    const payload = { coupon_code: code };
                    if (phone) payload.phone = phone;

                    result = await postConversionAction(wrapper.dataset.applyUrl, payload);

                    if (result.ok) {
                        /* সফল হলে ইনপুটের জায়গায় "প্রয়োগ হয়েছে" ব্যাজ বসাই */
                        const form = wrapper.querySelector('.cpb-coupon-form');
                        if (form) {
                            const applied = document.createElement('div');
                            applied.className = 'cpb-coupon-applied';
                            applied.setAttribute('data-cpb-coupon-applied', '');
                            applied.innerHTML = '<span>✓ কুপন <b>' + escapeHtml(code.toUpperCase()) + '</b> প্রয়োগ হয়েছে</span>'
                                + '<button type="button" data-cpb-coupon-remove>বাতিল</button>';
                            form.replaceWith(applied);
                        }
                    }
                } else {
                    result = await postConversionAction(wrapper.dataset.removeUrl, {});

                    if (result.ok) {
                        const applied = wrapper.querySelector('[data-cpb-coupon-applied]');
                        if (applied) {
                            const form = document.createElement('div');
                            form.className = 'cpb-coupon-form';
                            form.innerHTML = '<input type="text" id="cpb-coupon-code" placeholder="কুপন কোড থাকলে লিখুন" autocomplete="off" aria-label="কুপন কোড">'
                                + '<button type="button" data-cpb-coupon-apply>প্রয়োগ</button>';
                            applied.replaceWith(form);
                        }
                    }
                }

                say(result.message, result.ok);
            } catch (error) {
                say('কুপন যাচাই করা যায়নি, আবার চেষ্টা করুন।', false);
            } finally {
                button.disabled = false;
                setBusy(false);
            }
        });
    }

    /**
     * বাম্প প্রোডাক্ট window._campaignProducts এ থাকে না (সেটা ক্যাম্পেইনের
     * নিজস্ব প্রোডাক্ট তালিকা), তাই ট্র্যাকিং ডেটা DOM থেকেই নিই।
     */
    function trackBumpAddToCart(card) {
        if (!card) return;

        const item = {
            item_id: String(card.dataset.bumpProductId || ''),
            item_name: card.dataset.bumpProductName || '',
            price: Number(card.dataset.bumpPrice || 0),
            quantity: 1
        };

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({ event: 'add_to_cart', ecommerce: { currency: 'BDT', value: item.price, items: [item] } });

        if (typeof window.fbq === 'function') {
            window.fbq('track', 'AddToCart', {
                content_ids: [item.item_id],
                content_name: item.item_name,
                content_type: 'product',
                value: item.price,
                currency: 'BDT'
            }, { eventID: 'atc_bump_' + item.item_id + '_' + Math.floor(Date.now() / 1000) });
        }

        if (typeof window.ttq !== 'undefined' && typeof window.ttq.track === 'function') {
            window.ttq.track('AddToCart', {
                content_id: item.item_id,
                content_name: item.item_name,
                content_type: 'product',
                value: item.price,
                currency: 'BDT',
                quantity: 1
            });
        }
    }

    function initOrderBumps() {
        const wrapper = root.querySelector('[data-cpb-bumps]');
        if (!wrapper) return;

        wrapper.addEventListener('change', async event => {
            const toggle = event.target.closest('[data-cpb-bump-toggle]');
            if (!toggle || !toggle.checked) return;

            /* একবার যোগ হয়ে গেলে আর দ্বিতীয়বার পাঠাবো না */
            if (toggle.closest('[data-cpb-bump]')?.classList.contains('is-added')) return;

            if (requestInProgress) {
                toggle.checked = false;
                return;
            }

            const bumpId = toggle.dataset.cpbBumpToggle;
            const card = toggle.closest('[data-cpb-bump]');
            toggle.disabled = true;
            setBusy(true);

            try {
                const result = await postConversionAction(wrapper.dataset.bumpUrl, { bump_id: bumpId });

                if (result.ok) {
                    /* একবার যোগ হলে আর খোলা যাবে না — সরানোর কাজটা কার্ট টেবিল করে */
                    card?.classList.add('is-added');
                    showToast(result.message || 'অফারটি যোগ হয়েছে!', true);

                    /* বাম্প id নয়, আসল প্রোডাক্ট id দিয়েই AddToCart ট্র্যাক করি */
                    trackBumpAddToCart(card);
                } else {
                    toggle.checked = false;
                    toggle.disabled = false;
                    showToast(result.message || 'অফারটি যোগ করা যায়নি।');
                }
            } catch (error) {
                toggle.checked = false;
                toggle.disabled = false;
                showToast('অফারটি যোগ করা যায়নি, আবার চেষ্টা করুন।');
            } finally {
                setBusy(false);
            }
        });
    }

    function initAnalytics() {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ ecommerce: null });
        window.dataLayer.push({
            event: 'view_item_list',
            ecommerce: { currency: 'BDT', items: products.map((product, index) => ({ item_id: product.id, item_name: product.name, price: Number(product.price || 0), index, quantity: 1 })) }
        });
    }

    mountDynamicContent();
    initProductCards();
    initSizeChart();
    initUpsellBar();
    bindEvents();
    initAbandonedCart();
    initCoupon();
    initOrderBumps();
    initAnalytics();
    document.addEventListener('campaign:product-selected', updateUpsellBar);
})();
