export default function initCalendarTabs() {
  document.querySelectorAll('[data-calendar-tabs]').forEach((root) => {
    const tablist = root.querySelector('[data-calendar-tablist]');
    const tabs = [...root.querySelectorAll('[data-calendar-tab]')];
    const panels = [...root.querySelectorAll('[data-calendar-panel]')];

    if (!tablist || tabs.length < 2) {
      return;
    }

    const select = (key, { focus = false, updateHash = false } = {}) => {
      tabs.forEach((tab) => {
        const active = tab.dataset.calendarTab === key;
        tab.setAttribute('aria-selected', active ? 'true' : 'false');
        tab.tabIndex = active ? 0 : -1;
        if (active && focus) {
          tab.focus();
        }
      });
      panels.forEach((panel) => {
        panel.hidden = panel.dataset.calendarPanel !== key;
      });
      if (updateHash) {
        history.replaceState(null, '', key === root.dataset.defaultTab ? location.pathname + location.search : `#${key}`);
      }
    };

    const fromHash = location.hash.slice(1);
    const initial = tabs.some((tab) => tab.dataset.calendarTab === fromHash) ? fromHash : root.dataset.defaultTab;

    tablist.hidden = false;
    select(initial);

    tabs.forEach((tab, index) => {
      tab.addEventListener('click', () => select(tab.dataset.calendarTab, { updateHash: true }));
      tab.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') {
          return;
        }
        event.preventDefault();
        const next = tabs[(index + (event.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
        select(next.dataset.calendarTab, { focus: true, updateHash: true });
      });
    });
  });
}
