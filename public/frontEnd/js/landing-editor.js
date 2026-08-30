(function () {
    const cfg = window._lpEditor;
    if (!cfg) return;
    const content = cfg.content || {};
    let timer = null;
    const stateEl = document.getElementById('lp-save-state');
    const file = document.getElementById('lp-file');
    let imageTarget = null;

    function mark(text) { if (stateEl) stateEl.textContent = text; }

    function save() {
        mark('সেভ হচ্ছে...');
        fetch(cfg.saveUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': cfg.csrf,
            },
            body: JSON.stringify({ content }),
        }).then((r) => r.json()).then(() => mark('সেভ আছে')).catch(() => mark('সেভ ব্যর্থ'));
    }

    function schedule() {
        mark('লিখছেন...');
        clearTimeout(timer);
        timer = setTimeout(save, 700);
    }

    document.querySelectorAll('[data-edit]').forEach((el) => {
        const key = el.dataset.edit;
        const type = el.dataset.type || 'text';
        if (type === 'image' || type === 'gallery') {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                imageTarget = { el, key, type };
                file.click();
            });
            return;
        }
        el.contentEditable = 'true';
        el.addEventListener('focus', () => el.classList.add('is-editing'));
        el.addEventListener('blur', () => {
            el.classList.remove('is-editing');
            content[key] = type === 'html' ? el.innerHTML : el.innerText.trim();
            schedule();
        });
        el.addEventListener('input', () => {
            content[key] = type === 'html' ? el.innerHTML : el.innerText.trim();
            schedule();
        });
    });

    file?.addEventListener('change', () => {
        if (!file.files[0] || !imageTarget) return;
        const fd = new FormData();
        fd.append('image', file.files[0]);
        mark('ছবি আপলোড...');
        fetch(cfg.uploadUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-CSRF-TOKEN': cfg.csrf, Accept: 'application/json' },
            body: fd,
        }).then((r) => r.json()).then((data) => {
            const path = data.path || '';
            const url = data.url;
            if (imageTarget.type === 'gallery') {
                content.gallery = content.gallery || [];
                content.gallery.push(path);
            } else {
                content[imageTarget.key] = path;
                const img = imageTarget.el.querySelector('img');
                if (img && url) img.src = url;
            }
            schedule();
        }).finally(() => { file.value = ''; });
    });

    const drawer = document.getElementById('lp-product-drawer');
    document.getElementById('lp-products-btn')?.addEventListener('click', () => {
        drawer.hidden = !drawer.hidden;
    });
    document.getElementById('lp-product-form')?.addEventListener('submit', (e) => {
        e.preventDefault();
        const ids = Array.from(e.target.querySelectorAll('input:checked')).map((i) => i.value);
        if (!ids.length) return;
        fetch(cfg.productsUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': cfg.csrf },
            body: JSON.stringify({ product_id: ids }),
        }).then((r) => r.json()).then(() => { mark('প্রোডাক্ট আপডেট'); location.reload(); });
    });

    const root = document.querySelector('[data-lp-root]');
    if (root) {
        Array.from(root.querySelectorAll('[data-lp-section]')).forEach((sec) => {
            const handle = document.createElement('button');
            handle.type = 'button';
            handle.className = 'lp-sec-handle';
            handle.textContent = '⇅ ' + (sec.dataset.lpSection || '');
            handle.draggable = true;
            sec.prepend(handle);
            handle.addEventListener('dragstart', (ev) => {
                ev.dataTransfer.setData('text/plain', sec.dataset.lpSection);
                sec.classList.add('is-dragging');
            });
            sec.addEventListener('dragover', (ev) => ev.preventDefault());
            sec.addEventListener('drop', (ev) => {
                ev.preventDefault();
                const from = ev.dataTransfer.getData('text/plain');
                const moving = root.querySelector('[data-lp-section="' + from + '"]');
                if (moving && moving !== sec) {
                    root.insertBefore(moving, sec);
                    content.sections = Array.from(root.querySelectorAll('[data-lp-section]')).map((s) => s.dataset.lpSection);
                    schedule();
                }
            });
        });
    }
})();
