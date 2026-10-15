// Display Pure's bundled login image. To customize, replace
// assets/icons/log-in-logo.png in this plugin.
(() => {
  const prefix = location.pathname.split('/panel')[0];
  const logo = `${prefix}/media/plugins/kirbypure/core/icons/log-in-logo.png`;

  function updateLogo() {
    const form = document.querySelector('.k-login-form');
    if (!form) return;
    let img = form.querySelector('.pure-login-logo');
    if (!img) {
      img = document.createElement('img');
      img.className = 'pure-login-logo';
      img.alt = '';
      form.prepend(img);
    }
    if (img.getAttribute('src') !== logo) {
      img.onerror = () => { img.style.display = 'none'; };
      img.src = logo;
    }
  }

  updateLogo();
  new MutationObserver(updateLogo).observe(document.documentElement, {childList: true, subtree: true});
})();
