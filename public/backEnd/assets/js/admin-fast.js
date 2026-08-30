(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  function byId(id) {
    return document.getElementById(id);
  }

  function normalizePath(url) {
    try {
      var u = new URL(url, window.location.origin);
      return (u.pathname + u.search).replace(/\/+$/, '') || '/';
    } catch (e) {
      return '';
    }
  }

  function currentPath() {
    return normalizePath(window.location.href);
  }

  function syncTopOffset() {
    var topbar = document.querySelector('.navbar-custom');
    if (!topbar) return;
    var height = Math.round(topbar.getBoundingClientRect().height) || 64;
    document.documentElement.style.setProperty('--ar-topbar-h', height + 'px');
  }

  function watchTopOffset() {
    var topbar = document.querySelector('.navbar-custom');
    if (!topbar) return;
    syncTopOffset();
    if (window.ResizeObserver) {
      new ResizeObserver(syncTopOffset).observe(topbar);
    }
    window.addEventListener('resize', syncTopOffset);
    window.addEventListener('orientationchange', function () {
      setTimeout(syncTopOffset, 120);
    });
  }

  function setupTopbarSearch() {
    var toggle = byId('gsearchToggle');
    var box = byId('gsearchBox');
    var input = byId('gsearch');
    if (!toggle || !box) return;

    function setOpen(open) {
      document.body.classList.toggle('topbar-search-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open && input) input.focus();
    }

    toggle.addEventListener('click', function (event) {
      event.preventDefault();
      event.stopPropagation();
      setOpen(!document.body.classList.contains('topbar-search-open'));
    });

    document.addEventListener('click', function (event) {
      if (!document.body.classList.contains('topbar-search-open')) return;
      if (event.target.closest('#gsearchBox') || event.target.closest('#gsearchToggle')) return;
      setOpen(false);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setOpen(false);
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 767) setOpen(false);
    });
  }

  function enhancePageShell() {
    document.querySelectorAll('.page-title-box').forEach(function (box) {
      box.classList.add('ar-page-hero');
    });

    [
      '.container-fluid > .d-flex:first-child',
      '.order-wrap > .d-flex:first-child',
      '.content-shell > .d-flex:first-child'
    ].forEach(function (selector) {
      document.querySelectorAll(selector).forEach(function (header) {
        var hasHeading = header.querySelector('h1, h2, h3, h4, h5');
        if (!hasHeading) return;
        header.classList.add('ar-simple-header');
      });
    });

    document.querySelectorAll('.table-responsive').forEach(function (wrap) {
      wrap.classList.add('ar-table-wrap');
    });

    document.querySelectorAll('.content-shell > .container-fluid, .content-shell > .dash, .content-shell > .order-wrap, .content-shell > .gemini-wrapper, .content-shell > .dashboard-shell').forEach(function (screen) {
      screen.classList.add('ar-screen');
    });
  }

  function setupSidebar() {
    var body = document.body;
    var sidebar = byId('adminSidebar');
    var overlay = byId('sidebarOverlay');
    var menu = byId('side-menu');
    var searchInput = byId('sidebarMenuSearch');
    var noResult = byId('sidebarSearchNoResult');
    var toggleBtn = document.querySelector('.button-menu-mobile');
    var closeBtn = byId('sidebarCloseBtn');
    if (!sidebar || !menu) return;

    var DESKTOP = 992;
    var STORE_KEY = 'ar-sidebar-collapsed';

    function isDesktop() {
      return window.innerWidth >= DESKTOP;
    }

    function setCollapsed(collapsed) {
      body.classList.toggle('sidebar-collapsed', collapsed);
      try { localStorage.setItem(STORE_KEY, collapsed ? '1' : '0'); } catch (e) {}
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    }

    function closeSidebar() {
      body.classList.remove('sidebar-open');
      if (!isDesktop() && toggleBtn) toggleBtn.setAttribute('aria-expanded', 'false');
    }

    function toggleSidebar() {
      if (isDesktop()) {
        setCollapsed(!body.classList.contains('sidebar-collapsed'));
        return;
      }
      var open = !body.classList.contains('sidebar-open');
      body.classList.toggle('sidebar-open', open);
      if (toggleBtn) toggleBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    /* restore the desktop collapsed state chosen last time */
    var stored = '0';
    try { stored = localStorage.getItem(STORE_KEY) || '0'; } catch (e) {}
    if (isDesktop()) {
      setCollapsed(stored === '1');
    } else if (toggleBtn) {
      toggleBtn.setAttribute('aria-expanded', 'false');
    }

    if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeSidebar();
        if (searchInput && document.activeElement === searchInput) {
          searchInput.value = '';
          searchInput.dispatchEvent(new Event('input'));
        }
      }
    });

    window.addEventListener('resize', function () {
      if (isDesktop()) {
        closeSidebar();
        var saved = '0';
        try { saved = localStorage.getItem(STORE_KEY) || '0'; } catch (e) {}
        body.classList.toggle('sidebar-collapsed', saved === '1');
      } else {
        body.classList.remove('sidebar-collapsed');
      }
      syncTopOffset();
    });

    var topItems = Array.prototype.slice.call(menu.children);
    topItems.forEach(function (item) {
      if (item.classList.contains('has-sub')) {
        item.dataset.initialOpen = item.classList.contains('open') ? '1' : '0';
      }
      item.dataset.baseActive = item.classList.contains('menuitem-active') ? '1' : '0';
    });

    function setOpen(item, open) {
      if (!item) return;
      var panel = item.querySelector(':scope > .menu-collapse');
      var trigger = item.querySelector(':scope > a[data-sidebar-toggle]');
      item.classList.toggle('open', open);
      item.classList.toggle('menuitem-active', open || item.dataset.baseActive === '1');
      if (panel) panel.classList.toggle('show', open);
      if (trigger) trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('#side-menu [data-sidebar-toggle]');
      if (trigger) {
        event.preventDefault();
        var item = trigger.closest('li.has-sub');
        var panel = item ? item.querySelector(':scope > .menu-collapse') : null;
        var willOpen = !(panel && panel.classList.contains('show'));

        topItems.forEach(function (candidate) {
          if (candidate !== item && candidate.classList.contains('has-sub')) {
            setOpen(candidate, false);
          }
        });

        if (item) setOpen(item, willOpen);
        return;
      }

      var directLink = event.target.closest('#side-menu a[href]:not([data-sidebar-toggle])');
      if (directLink && window.innerWidth <= 991) {
        closeSidebar();
      }
    }, true);

    function markActiveLinks() {
      var path = currentPath();
      var links = menu.querySelectorAll('a[href]:not([data-sidebar-toggle])');
      links.forEach(function (link) {
        var linkPath = normalizePath(link.href);
        if (!linkPath) return;
        if (linkPath === path) {
          link.classList.add('is-active');
          var parentItem = link.closest('li');
          if (parentItem) parentItem.classList.add('active');
          var rootItem = link.closest('#side-menu > li');
          if (rootItem) {
            rootItem.classList.add('menuitem-active');
            rootItem.dataset.baseActive = '1';
            if (rootItem.classList.contains('has-sub')) {
              setOpen(rootItem, true);
            }
          }
        }
      });
    }

    function restoreMenuState() {
      topItems.forEach(function (item) {
        item.classList.remove('ar-search-hidden');
        item.querySelectorAll('.ar-search-hidden').forEach(function (hidden) {
          hidden.classList.remove('ar-search-hidden');
        });
        if (!item.classList.contains('has-sub')) return;
        var shouldOpen = item.dataset.initialOpen === '1' || item.classList.contains('menuitem-active');
        setOpen(item, shouldOpen);
      });
      if (noResult) noResult.style.display = 'none';
    }

    function filterMenu(query) {
      var anyVisible = false;
      topItems.forEach(function (item) {
        if (item.classList.contains('has-sub')) {
          var itemText = (item.querySelector(':scope > a') || item).textContent.toLowerCase();
          var children = Array.prototype.slice.call(item.querySelectorAll('.nav-second-level > li'));
          var parentMatch = itemText.indexOf(query) !== -1;
          var anyChildVisible = false;

          children.forEach(function (child) {
            var match = parentMatch || child.textContent.toLowerCase().indexOf(query) !== -1;
            child.classList.toggle('ar-search-hidden', !match);
            if (match) anyChildVisible = true;
          });

          item.classList.toggle('ar-search-hidden', !anyChildVisible);
          setOpen(item, anyChildVisible);
          if (anyChildVisible) anyVisible = true;
          return;
        }

        var matchTop = item.textContent.toLowerCase().indexOf(query) !== -1;
        item.classList.toggle('ar-search-hidden', !matchTop);
        if (matchTop) anyVisible = true;
      });

      if (noResult) noResult.style.display = anyVisible ? 'none' : 'block';
    }

    if (searchInput) {
      var timer;
      searchInput.addEventListener('input', function () {
        clearTimeout(timer);
        var query = (searchInput.value || '').toLowerCase().trim().replace(/\s+/g, ' ');
        timer = setTimeout(function () {
          if (!query) {
            restoreMenuState();
            return;
          }
          filterMenu(query);
        }, 120);
      });
    }

    restoreMenuState();
    markActiveLinks();
  }

  function setupDropdowns() {
    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('[data-bs-toggle="dropdown"]');
      if (trigger) {
        event.preventDefault();
        var dropdown = trigger.closest('.dropdown') || trigger.parentElement;
        document.querySelectorAll('.dropdown.show').forEach(function (open) {
          if (open !== dropdown) open.classList.remove('show');
        });
        if (dropdown) dropdown.classList.toggle('show');
        return;
      }

      if (!event.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown.show').forEach(function (open) {
          open.classList.remove('show');
        });
      }
    });
  }

  function setupNavigationProgress() {
    var bar = byId('nprog');
    document.addEventListener('click', function (event) {
      var anchor = event.target.closest('a[href]');
      if (!anchor) return;
      var href = anchor.getAttribute('href') || '';
      if (!href || href.charAt(0) === '#' || href.indexOf('javascript:') === 0 || anchor.target === '_blank' || anchor.hasAttribute('download')) return;
      if (anchor.origin && anchor.origin !== window.location.origin) return;
      document.body.classList.add('nav-busy');
      if (bar) bar.style.width = '68%';
    }, true);

    window.addEventListener('pageshow', function () {
      document.body.classList.remove('nav-busy');
      if (bar) bar.style.width = '0';
    });
  }

  function setupGlobalSearch() {
    var input = byId('gsearch');
    var results = byId('gsearchRes');
    if (!input || !results) return;

    var timer;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      var query = input.value.trim();
      if (query.length < 2) {
        results.style.display = 'none';
        results.innerHTML = '';
        return;
      }

      timer = setTimeout(function () {
        var base = window.ADMIN_ORDER_SEARCH || '/admin/order-search';
        fetch(base + (base.indexOf('?') > -1 ? '&' : '?') + 'q=' + encodeURIComponent(query), {
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
          .then(function (response) { return response.json(); })
          .then(function (rows) {
            if (!Array.isArray(rows) || !rows.length) {
              results.innerHTML = '<div class="gsearch-empty">No matching order found.</div>';
              results.style.display = 'block';
              return;
            }

            results.innerHTML = rows.map(function (row) {
              return '<a href="' + row.url + '"><b>#' + row.invoice + '</b><span>' + (row.name || '') + ' · ' + (row.phone || '') + '</span><em>' + (row.status || '') + ' · ৳' + row.amount + '</em></a>';
            }).join('');
            results.style.display = 'block';
          })
          .catch(function () {
            results.innerHTML = '<div class="gsearch-empty">Search is unavailable right now.</div>';
            results.style.display = 'block';
          });
      }, 180);
    });

    document.addEventListener('click', function (event) {
      if (!event.target.closest('#gsearchBox')) {
        results.style.display = 'none';
      }
    });
  }

  function setupConfirmActions() {
    document.addEventListener('click', function (event) {
      var deleteBtn = event.target.closest('.delete-confirm');
      if (deleteBtn) {
        if (!window.confirm('Are you sure you want to delete this record?')) {
          event.preventDefault();
          event.stopPropagation();
        }
        return;
      }

      var changeBtn = event.target.closest('.change-confirm');
      if (changeBtn) {
        if (!window.confirm('Do you want to apply this change?')) {
          event.preventDefault();
          event.stopPropagation();
        }
      }
    }, true);
  }

  function prefetchLinks() {
    document.addEventListener('mouseover', function (event) {
      var anchor = event.target.closest && event.target.closest('a[href]');
      if (!anchor) return;
      var href = anchor.getAttribute('href') || '';
      if (!href || href.charAt(0) === '#' || anchor.target === '_blank') return;
      if (anchor.dataset.prefetched) return;
      if (anchor.origin && anchor.origin !== window.location.origin) return;
      anchor.dataset.prefetched = '1';
      var link = document.createElement('link');
      link.rel = 'prefetch';
      link.href = anchor.href;
      document.head.appendChild(link);
    }, { passive: true });
  }

  ready(function () {
    watchTopOffset();
    setupTopbarSearch();
    enhancePageShell();
    setupSidebar();
    setupDropdowns();
    setupNavigationProgress();
    setupGlobalSearch();
    setupConfirmActions();
    prefetchLinks();

    if (window.feather && typeof window.feather.replace === 'function') {
      window.feather.replace();
    }
  });
})();
