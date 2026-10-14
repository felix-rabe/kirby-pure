// Kirby Panel mounts asynchronously; observe until the login form appears.
(() => {
  const insertLogo = () => {
    const form = document.querySelector('.k-login-form');
    if (!form || form.querySelector('.pure-login-logo')) return;

    const img = document.createElement('img');
    img.className = 'pure-login-logo';
    img.alt = '';
    // Resolve correctly when Kirby is installed in a subdirectory.
    const panelPath = window.location.pathname;
    const panelIndex = panelPath.indexOf('/panel');
    const base = panelIndex >= 0 ? panelPath.slice(0, panelIndex) : '';
    img.src = `${base}/pure-login-logo`;
    form.prepend(img);
  };

  insertLogo();
  new MutationObserver(insertLogo).observe(document.documentElement, {
    childList: true,
    subtree: true
  });
})();
