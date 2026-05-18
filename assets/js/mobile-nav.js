document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('mobileSidebar');
  const overlay = document.getElementById('sidebarOverlay');
  const closeSidebar = document.getElementById('closeSidebarBtn');

  function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (overlay) overlay.classList.add('active');
  }

  function closeSidebarFunc() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('active');
  }

  if (hamburger) hamburger.addEventListener('click', openSidebar);
  if (closeSidebar) closeSidebar.addEventListener('click', closeSidebarFunc);
  if (overlay) overlay.addEventListener('click', closeSidebarFunc);

  // Keep mobile nav in sync with mainNav
  const mainNav = document.getElementById('mainNav');
  const mobileNav = document.getElementById('mobileNavLinks');
  function syncNav() {
    if (mainNav && mobileNav) mobileNav.innerHTML = mainNav.innerHTML;
  }
  syncNav();
  if (mainNav && mobileNav && window.MutationObserver) {
    const obs = new MutationObserver(syncNav);
    obs.observe(mainNav, { childList: true, subtree: true, characterData: true });
  }
});
