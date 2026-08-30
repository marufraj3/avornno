(function () {
  'use strict';
  var root = document.querySelector('[data-ajax-list]');
  if (!root) return;

  function load(url, push) {
    if (!url) return;
    root.classList.add('is-loading');
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
      .then(function (r) { return r.text(); })
      .then(function (html) {
        root.innerHTML = html;
        if (push && window.history && history.pushState) history.pushState({ ajaxList: true }, '', url);
        root.classList.remove('is-loading');
        if (typeof window.syncSticky === 'function') window.syncSticky();
      })
      .catch(function () {
        root.classList.remove('is-loading');
        window.location.href = url;
      });
  }

  document.addEventListener('click', function (e) {
    var a = e.target.closest('[data-ajax-list] .pagination a, .otabs a[data-ajax], [data-ajax-list] a.page-link');
    if (!a || a.target === '_blank') return;
    e.preventDefault();
    load(a.href, true);
  });

  document.addEventListener('submit', function (e) {
    var form = e.target.closest('[data-ajax-form]');
    if (!form) return;
    e.preventDefault();
    var action = form.getAttribute('action') || window.location.pathname;
    var data = new FormData(form);
    var qs = new URLSearchParams(data).toString();
    load(action + (action.indexOf('?') > -1 ? '&' : '?') + qs, true);
  });

  window.addEventListener('popstate', function () {
    if (root) load(window.location.href, false);
  });
})();
