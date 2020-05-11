customElements.define('x-auto-toggler', class extends HTMLInputElement {
    onChange() {
        this.disabled = true;
        fetch(this.dataset.toggle || (this.checked ? this.dataset.on : this.dataset.off),
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Csrf-token': this.dataset.csrf,
                },
                body: JSON.stringify(this.checked)
            })
            .then(() => this.disabled = false)
            .catch(() => this.disabled = false)
    }

    connectedCallback() {
        this.addEventListener('change', () => this.onChange());
    }
}, {extends: 'input'});
