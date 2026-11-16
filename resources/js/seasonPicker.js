/**
 * Season picker (button + option list) used on /meeskonnad and /arhiiv.
 *
 * Owns the open/close behaviour and keyboard support:
 *   - trigger toggles the list (aria-expanded), ArrowDown/ArrowUp on the trigger opens it
 *   - focus moves to the active option on open; ArrowUp/ArrowDown/Home/End move between options
 *   - Escape closes and returns focus to the trigger; Tab out or an outside click closes
 *   - the chosen option gets .is-active + aria-current and its text becomes the trigger label
 *
 * Selecting an option dispatches `skduo:season-change` (detail: { value, label }) on the
 * [data-season-picker] element; the page templates listen for it to filter their content.
 */
export default function initSeasonPickers() {
  document.querySelectorAll('[data-season-picker]').forEach((picker, pickerIndex) => {
    const trigger = picker.querySelector('[data-season-trigger]');
    const menu = picker.querySelector('[data-season-menu]');
    const label = picker.querySelector('[data-season-label]');
    const options = [...picker.querySelectorAll('[data-season-option]')];

    if (!trigger || !menu || !options.length) return;

    if (!menu.id) menu.id = `skduo-season-menu-${pickerIndex}`;
    trigger.setAttribute('aria-controls', menu.id);

    const syncCurrent = () => {
      options.forEach((option) => {
        if (option.classList.contains('is-active')) option.setAttribute('aria-current', 'true');
        else option.removeAttribute('aria-current');
      });
    };
    syncCurrent();

    const isOpen = () => picker.classList.contains('is-open');
    const activeOption = () => options.find((option) => option.classList.contains('is-active')) || options[0];

    const open = ({ focus = true } = {}) => {
      picker.classList.add('is-open');
      trigger.setAttribute('aria-expanded', 'true');
      if (focus) activeOption().focus();
    };

    const close = ({ returnFocus = false } = {}) => {
      if (!isOpen()) return;
      picker.classList.remove('is-open');
      trigger.setAttribute('aria-expanded', 'false');
      if (returnFocus) trigger.focus();
    };

    const focusOption = (index) => {
      options[(index + options.length) % options.length].focus();
    };

    trigger.addEventListener('click', () => {
      if (isOpen()) close();
      // Keyboard activation (Enter/Space) moves focus into the list; a mouse click does not need to.
      else open({ focus: document.activeElement === trigger });
    });

    trigger.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp') return;
      event.preventDefault();
      open({ focus: false });
      focusOption(event.key === 'ArrowDown' ? options.indexOf(activeOption()) : options.length - 1);
    });

    menu.addEventListener('keydown', (event) => {
      const index = options.indexOf(document.activeElement);
      if (event.key === 'ArrowDown') { event.preventDefault(); focusOption(index + 1); }
      else if (event.key === 'ArrowUp') { event.preventDefault(); focusOption(index - 1); }
      else if (event.key === 'Home') { event.preventDefault(); focusOption(0); }
      else if (event.key === 'End') { event.preventDefault(); focusOption(options.length - 1); }
    });

    options.forEach((option) => {
      option.addEventListener('click', () => {
        options.forEach((item) => item.classList.toggle('is-active', item === option));
        syncCurrent();
        const text = option.textContent.trim();
        if (label) label.textContent = text;
        close({ returnFocus: true });
        picker.dispatchEvent(new CustomEvent('skduo:season-change', {
          detail: { value: option.getAttribute('data-season-value') || 'all', label: text },
        }));
      });
    });

    picker.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && isOpen()) {
        event.stopPropagation();
        close({ returnFocus: true });
      }
    });

    picker.addEventListener('focusout', (event) => {
      if (!picker.contains(event.relatedTarget)) close();
    });

    document.addEventListener('click', (event) => {
      if (!picker.contains(event.target)) close();
    });
  });
}
