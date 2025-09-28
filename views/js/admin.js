document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.gcromo-budget');
  if (!container) {
    return;
  }

  container.classList.add('gcromo-ready');
});
