customElements.define('x-auto-password', class extends HTMLButtonElement {
    connectedCallback() {
        this.addEventListener('click', (e) => {
            e.preventDefault();
            this.onClick();
        });
    }

    onClick() {
        const input = document.querySelector('#' + this.dataset.for);
        input.type = 'text';
        input.disabled = true;
        fetch('/api/password/generate')
            .then(resp => resp.json())
            .then(data => {
                input.value = data.password;
                input.dispatchEvent(new Event('change'));
                input.disabled = false;
            })
    }
}, {extends: 'button'});