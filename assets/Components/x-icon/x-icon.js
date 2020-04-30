import './x-icon.scss';

(() => {
    const iconset = {
        "sort": "m 16,24 v 1 L 9,32 2,25 V 24 H 6 V 0 h 6 V 24 Z M 23,0 30,7 V 8 H 26 V 32 H 20 V 8 H 16 V 7 Z",
        "sort-by-attributes": "m 14,24 v 1 L 7,32 0,25 V 24 H 4 V 0 h 6 V 24 Z M 24,6 H 18 V 0 h 6 z m 2,8 H 18 V 8 h 8 z m 4,8 H 18 v -6 h 12 z m 2,8 H 18 v -6 h 14 z",
        "sort-by-attributes-alt": "m 14,24 v 1 L 7,32 0,25 V 24 H 4 V 0 h 6 V 24 Z M 32,6 H 18 V 0 h 14 z m -2,8 H 18 V 8 h 12 z m -4,8 h -8 v -6 h 8 z m -2,8 h -6 v -6 h 6 z",
    };
    customElements.define('x-icon', class extends HTMLElement {
        get template() {
            return `<svg viewBox="0 0 32 32" style="pointer-events: none; display: block; width: 100%; height: 100%;"><path d="${this.iconGlyph}"/></svg>`
        }

        get icon() {
            return this.getAttribute('icon')
        }

        get iconGlyph() {
            if (!iconset[this.icon]) {
                console.warn(`icon "${this.icon}" not defined`);
            }
            return iconset[this.icon] || '';
        }

        connectedCallback() {
            this.innerHTML = this.template;
        }
    });
})();
