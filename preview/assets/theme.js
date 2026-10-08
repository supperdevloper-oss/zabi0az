(() => {
  'use strict';
  const root = document.documentElement;
  root.classList.add('has-js');

  const menuToggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('#primary-navigation');
  if (menuToggle && navigation) {
    menuToggle.addEventListener('click', () => {
      const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
      menuToggle.setAttribute('aria-expanded', String(!isOpen));
      navigation.classList.toggle('is-open', !isOpen);
      if (!isOpen) {
        const firstLink = navigation.querySelector('a');
        if (firstLink) firstLink.focus();
      }
    });
    navigation.addEventListener('click', (event) => {
      if (event.target.closest('a') && window.matchMedia('(max-width: 900px)').matches) {
        navigation.classList.remove('is-open');
        menuToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  const searchToggle = document.querySelector('.search-toggle');
  const searchPanel = document.querySelector('#site-search-panel');
  if (searchToggle && searchPanel) {
    searchToggle.addEventListener('click', () => {
      const isOpen = searchToggle.getAttribute('aria-expanded') === 'true';
      searchToggle.setAttribute('aria-expanded', String(!isOpen));
      searchToggle.setAttribute('aria-label', isOpen ? 'Open recipe search' : 'Close recipe search');
      searchPanel.classList.toggle('is-open', !isOpen);
      if (!isOpen) {
        const input = searchPanel.querySelector('input[type="search"]');
        if (input) input.focus();
      }
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && searchToggle.getAttribute('aria-expanded') === 'true') {
        searchToggle.setAttribute('aria-expanded', 'false');
        searchPanel.classList.remove('is-open');
        searchToggle.focus();
      }
    });
  }

  document.querySelectorAll('[data-print-recipe]').forEach((button) => {
    button.addEventListener('click', () => window.print());
  });

  const announce = (message) => {
    let status = document.querySelector('#ca-live-status');
    if (!status) {
      status = document.createElement('div');
      status.id = 'ca-live-status';
      status.className = 'screen-reader-text';
      status.setAttribute('role', 'status');
      status.setAttribute('aria-live', 'polite');
      document.body.appendChild(status);
    }
    status.textContent = message;
  };

  document.querySelectorAll('[data-share-recipe]').forEach((button) => {
    button.addEventListener('click', async () => {
      const shareData = { title: document.title, url: window.location.href };
      try {
        if (navigator.share) {
          await navigator.share(shareData);
          announce('Recipe shared.');
        } else if (navigator.clipboard && window.isSecureContext) {
          await navigator.clipboard.writeText(shareData.url);
          announce('Recipe link copied.');
        } else {
          window.prompt('Copy this recipe link:', shareData.url);
        }
      } catch (error) {
        if (error && error.name !== 'AbortError') announce('The recipe link could not be shared.');
      }
    });
  });
})();
