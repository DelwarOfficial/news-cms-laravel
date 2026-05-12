import './components/photo-news';
import './components/prayer-countdown';

/**
 * Dhaka Magazine frontend interactions.
 *
 * Features:
 * - Mobile menu toggle
 * - Sticky navbar behavior
 * - Scroll-to-top button
 * - Dark/light theme toggle
 * - Mobile category accordion
 */

document.addEventListener('DOMContentLoaded', function () {
  const hamburgerBtn = document.getElementById('hamburger-btn');
  const mobileMenu = document.getElementById('mobile-menu');
  const iconMenu = document.getElementById('icon-menu');
  const iconClose = document.getElementById('icon-close');
  const body = document.body;

  if (hamburgerBtn && mobileMenu) {
    hamburgerBtn.addEventListener('click', function () {
      var isOpen = !mobileMenu.classList.contains('hidden');
      mobileMenu.classList.toggle('hidden', isOpen);
      body.style.overflow = isOpen ? '' : 'hidden';
      if (iconMenu) iconMenu.classList.toggle('hidden', !isOpen);
      if (iconClose) iconClose.classList.toggle('hidden', isOpen);
    });

    var mobileLinks = mobileMenu.querySelectorAll('a');
    mobileLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        mobileMenu.classList.add('hidden');
        body.style.overflow = '';
        if (iconMenu) iconMenu.classList.remove('hidden');
        if (iconClose) iconClose.classList.add('hidden');
      });
    });
  }

  var siteNav = document.getElementById('site-nav');
  var siteHeader = document.getElementById('site-header');

  if (siteNav) {
    var scrollThreshold = 120;
    var ticking = false;

    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(function () {
          var scrolled = window.scrollY > scrollThreshold;
          siteNav.classList.toggle('sticky', scrolled);

          if (scrolled && siteHeader) {
            siteHeader.style.marginBottom = siteNav.offsetHeight + 'px';
          } else if (siteHeader) {
            siteHeader.style.marginBottom = '0';
          }

          ticking = false;
        });
        ticking = true;
      }
    });
  }

  var scrollBtn = document.createElement('button');
  scrollBtn.id = 'scroll-to-top';
  scrollBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m18 15-6-6-6 6"/></svg>';
  scrollBtn.style.cssText = 'position:fixed;bottom:24px;right:24px;width:44px;height:44px;border-radius:50%;background:#e2231a;color:#fff;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.3);opacity:0;pointer-events:none;transition:opacity 0.3s,transform 0.3s;transform:translateY(10px);z-index:999;cursor:pointer;';
  document.body.appendChild(scrollBtn);

  var scrollBtnTicking = false;
  window.addEventListener('scroll', function () {
    if (!scrollBtnTicking) {
      window.requestAnimationFrame(function () {
        var visible = window.scrollY > 400;
        scrollBtn.style.opacity = visible ? '1' : '0';
        scrollBtn.style.transform = visible ? 'translateY(0)' : 'translateY(10px)';
        scrollBtn.style.pointerEvents = visible ? 'auto' : 'none';
        scrollBtnTicking = false;
      });
      scrollBtnTicking = true;
    }
  });

  scrollBtn.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  var themeToggle = document.getElementById('theme-toggle');
  var themeToggleNav = document.getElementById('theme-toggle-nav');
  var themeIconSun = document.getElementById('theme-icon-sun');
  var themeIconMoon = document.getElementById('theme-icon-moon');
  var themeIconSunNav = document.getElementById('theme-icon-sun-nav');
  var themeIconMoonNav = document.getElementById('theme-icon-moon-nav');

  function updateThemeIcons() {
    var isDark = document.documentElement.classList.contains('dark');
    if (themeIconSun) themeIconSun.classList.toggle('hidden', isDark);
    if (themeIconMoon) themeIconMoon.classList.toggle('hidden', !isDark);
    if (themeIconSunNav) themeIconSunNav.classList.toggle('hidden', isDark);
    if (themeIconMoonNav) themeIconMoonNav.classList.toggle('hidden', !isDark);
  }

  function handleThemeToggle() {
    var html = document.documentElement;
    var isDark = html.classList.contains('dark');
    if (isDark) {
      html.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    } else {
      html.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    }
    updateThemeIcons();
  }

  window.toggleTheme = handleThemeToggle;

  if (themeToggle) {
    updateThemeIcons();
    themeToggle.addEventListener('click', handleThemeToggle);
  }
  if (themeToggleNav) {
    updateThemeIcons();
    themeToggleNav.addEventListener('click', handleThemeToggle);
  }

  var stickyMenuButton = document.getElementById('hamburger-btn-sticky');
  var iconMenuSticky = document.getElementById('icon-menu-sticky');
  var iconCloseSticky = document.getElementById('icon-close-sticky');

  function toggleStickyMenu() {
    if (!mobileMenu) return;
    mobileMenu.classList.toggle('hidden');
    if (iconMenuSticky) iconMenuSticky.classList.toggle('hidden');
    if (iconCloseSticky) iconCloseSticky.classList.toggle('hidden');
  }

  if (stickyMenuButton) {
    stickyMenuButton.addEventListener('click', toggleStickyMenu);
  }

  document.querySelectorAll('.mobile-accordion-btn').forEach(function (button) {
    button.addEventListener('click', function () {
      var submenu = button.nextElementSibling;
      var icon = button.querySelector('svg');

      if (submenu) submenu.classList.toggle('hidden');
      if (icon) icon.classList.toggle('rotate-180');
    });
  });

  var stickyScrollNav = document.getElementById('site-nav');
  if (stickyScrollNav) {
    window.addEventListener('scroll', function () {
      var currentScroll = window.pageYOffset || document.documentElement.scrollTop;
      stickyScrollNav.classList.toggle('is-sticky-scrolled', currentScroll > 80);
    });
  }
});
