const GRID_ID = 'gcromo_budget';

function initGrid(gridId) {
  const prestashop = window.prestashop || {};
  let GridRuntime = prestashop.component && prestashop.component.Grid;
  let GridExtensions = prestashop.component && prestashop.component.GridExtensions;

  if (typeof GridRuntime !== 'function') {
    GridRuntime = window.Grid;
  }

  if (typeof GridRuntime !== 'function') {
    return false;
  }

  try {
    // eslint-disable-next-line new-cap
    const grid = new GridRuntime(gridId);
    const extensionNames = [
      'SortingExtension',
      'FiltersResetExtension',
      'ReloadListActionExtension',
      'ColumnTogglingExtension',
      'SubmitRowActionExtension',
      'SubmitBulkExtension',
      'BulkActionCheckboxExtension',
      'ExportToSqlManagerExtension',
    ];

    if (!GridExtensions) {
      GridExtensions = extensionNames.reduce((acc, name) => {
        const globalCtor = window[name];
        if (typeof globalCtor === 'function') {
          acc[name] = globalCtor;
        }

        return acc;
      }, {});
    }

    extensionNames.forEach((name) => {
      const ExtensionCtor = GridExtensions && GridExtensions[name];
      if (typeof ExtensionCtor === 'function') {
        // eslint-disable-next-line new-cap
        grid.addExtension(new ExtensionCtor());
      }
    });

    if (window.console && typeof window.console.debug === 'function') {
      window.console.debug('[gcromo] Grid initialised for', gridId);
    }

    return true;
  } catch (error) {
    if (window.console && typeof window.console.error === 'function') {
      window.console.error('[gcromo] Grid initialisation failed', error);
    }

    return false;
  }
}

function ensureGridReady(gridId) {
  if (initGrid(gridId)) {
    return;
  }

  const start = Date.now();
  const interval = window.setInterval(() => {
    if (initGrid(gridId)) {
      window.clearInterval(interval);
      return;
    }

    if (Date.now() - start > 5000) {
      window.clearInterval(interval);
      if (window.console && typeof window.console.warn === 'function') {
        window.console.warn('[gcromo] Grid initialisation timeout for', gridId);
      }
    }
  }, 200);
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.gcromo-budget');
  if (!container) {
    return;
  }

  container.classList.add('gcromo-ready');

  const gridElement = container.querySelector(`.js-grid[data-grid-id="${GRID_ID}"]`);
  if (gridElement) {
    ensureGridReady(GRID_ID);
  }
});
