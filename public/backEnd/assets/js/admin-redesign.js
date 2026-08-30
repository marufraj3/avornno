/* ============================================================
   ADMIN PANEL REDESIGN — sidebar menu search + lightweight UX
   Pure client-side. No business logic here.
   ============================================================ */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else {
      fn();
    }
  }

  ready(function () {
    var input = document.getElementById('sidebarMenuSearch');
    var menu = document.getElementById('side-menu');
    var noResult = document.getElementById('sidebarSearchNoResult');
    if (!input || !menu) return;

    var allItems = Array.prototype.slice.call(menu.children); // top-level <li>

    function normalize(s) {
      return (s || '').toLowerCase().replace(/\s+/g, ' ').trim();
    }

    function clearSearch() {
      allItems.forEach(function (li) {
        li.classList.remove('ar-search-hidden');
        // close collapses we force-opened during search
        li.querySelectorAll('.collapse.ar-search-open').forEach(function (c) {
          c.classList.remove('show', 'ar-search-open');
        });
        li.querySelectorAll('li.ar-search-hidden').forEach(function (sub) {
          sub.classList.remove('ar-search-hidden');
        });
      });
      if (noResult) noResult.style.display = 'none';
    }

    function applySearch(q) {
      var anyVisible = false;
      var lastTitle = null;
      var lastTitleHasVisible = false;

      allItems.forEach(function (li) {
        if (li.classList.contains('menu-title')) {
          // resolve previous title visibility
          if (lastTitle) {
            lastTitle.classList.toggle('ar-search-hidden', !lastTitleHasVisible);
          }
          lastTitle = li;
          lastTitleHasVisible = false;
          return;
        }

        var subItems = li.querySelectorAll('.nav-second-level > li');
        var liText = '';
        var directLink = li.querySelector(':scope > a');
        if (directLink) liText = normalize(directLink.textContent);

        if (subItems.length) {
          var anySub = false;
          var parentMatches = liText.indexOf(q) !== -1;
          subItems.forEach(function (sub) {
            var match = parentMatches || normalize(sub.textContent).indexOf(q) !== -1;
            sub.classList.toggle('ar-search-hidden', !match);
            if (match) anySub = true;
          });
          li.classList.toggle('ar-search-hidden', !anySub);
          var collapse = li.querySelector(':scope > .collapse');
          if (collapse) {
            if (anySub) {
              if (!collapse.classList.contains('show')) {
                collapse.classList.add('show', 'ar-search-open');
              }
            } else if (collapse.classList.contains('ar-search-open')) {
              collapse.classList.remove('show', 'ar-search-open');
            }
          }
          if (anySub) {
            anyVisible = true;
            lastTitleHasVisible = true;
          }
        } else {
          var match = liText.indexOf(q) !== -1;
          li.classList.toggle('ar-search-hidden', !match);
          if (match) {
            anyVisible = true;
            lastTitleHasVisible = true;
          }
        }
      });

      if (lastTitle) {
        lastTitle.classList.toggle('ar-search-hidden', !lastTitleHasVisible);
      }
      if (noResult) noResult.style.display = anyVisible ? 'none' : 'block';
    }

    var debounce;
    input.addEventListener('input', function () {
      clearTimeout(debounce);
      var q = normalize(input.value);
      debounce = setTimeout(function () {
        if (!q) {
          clearSearch();
        } else {
          applySearch(q);
        }
      }, 120);
    });

    // ESC clears the search
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        input.value = '';
        clearSearch();
      }
    });
  });
})();
