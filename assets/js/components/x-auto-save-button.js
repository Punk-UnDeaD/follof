customElements.define('x-auto-save-button', class extends HTMLButtonElement {
    connectedCallback() {
        this.addEventListener('click', (e) => {
            e.preventDefault();
            this.onClick();
        });
    }

    onClick() {
        const input = document.querySelector('#' + this.dataset.for);
        input.disabled = true;

        fetch(this.dataset.save,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Csrf-token': this.dataset.csrf,
                },
                body: JSON.stringify({value: input.value}) // body data type must match "Content-Type" header
            }
        )
            .then(resp => resp.json())
            .catch(resp => resp.json())
            .then(data => {
                input.disabled = false;
                input.classList.remove('is-invalid', 'is-invalid');

                if ('error' === data.status) {
                    input.focus();
                    input.classList.add('is-invalid')
                } else {
                    input.classList.add('is-valid')
                }

            })
    }
}, {extends: 'button'});