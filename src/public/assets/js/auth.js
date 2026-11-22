// Progressive enhancement for the login and register forms.
(function () {
  'use strict';

  // Show / hide password
  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    var input = document.getElementById(btn.getAttribute('data-toggle-password'));
    if (!input) return;
    btn.addEventListener('click', function () {
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
      btn.setAttribute('aria-pressed', String(show));
    });
  });

  // Prevent accidental double submits (e.g. two accounts / two login attempts)
  document.querySelectorAll('form[data-single-submit]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var submit = form.querySelector('button[type="submit"]');
      if (!submit) return;
      if (submit.disabled) return;
      setTimeout(function () { submit.disabled = true; }, 0);
    });
  });
})();
