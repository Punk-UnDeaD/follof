customElements.define('x-auto-toggler', class extends HTMLInputElement {
    onChange() {
        const url = this.checked ? this.dataset.on : this.dataset.off;
        const csrf = this.dataset.csrf;
        this.disabled = true;
        fetch(url,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Csrf-token': csrf
                }
            })
            .then(() => this.disabled = false)
            .catch(() => this.disabled = false)
    }

    connectedCallback() {
        this.addEventListener('change', () => this.onChange());
    }
}, {extends: 'input'});