import Toolpip from '../Helpers/toolpip/toolpip';
import '../Helpers/toolpip/toolpip.scss';


customElements.define('x-auto-save-button', class extends HTMLButtonElement {
    connectedCallback() {
        this.addEventListener('click', (e) => {
            e.preventDefault();
            this.onClick();
        });
        this._input = document.querySelector('#' + this.dataset.for);
        this._toolpip = new Toolpip(this._input);
        this._input.addEventListener('change', () => this.onChange())
    }

    onChange() {
        this._input.classList.remove('is-invalid', 'is-valid');
        this._input.classList.add('is-changed');
        this._toolpip.hide();
    }

    onClick() {
        this._input.disabled = true;
        this._toolpip.hide();
        const request = {
            method: 'POST',
            headers: {
                'Csrf-token': this.dataset.csrf,
            }
        }
        if ('file' === this._input.type) {
            const formData = new FormData();
            let name = this._input.name || ('file' + (this._input.hasAttribute('multiple') ? '[]' : ''))
            this._input.files.forEach(f => formData.append(name, f));
            request.body = formData;
        } else {
            request.headers['Content-Type'] = 'application/json';
            request.body = JSON.stringify({value: this._input.value})
        }
        fetch(this.dataset.save, request)
            .then(resp => resp.json())
            .catch(resp => resp.json())
            .then(data => {
                this._input.disabled = false;
                this._input.classList.remove('is-invalid', 'is-invalid', 'is-changed');

                if ('error' === data.status) {
                    this._input.focus();
                    this._toolpip.updateContent('Error', data.message);
                    this._toolpip.show();
                    this._input.classList.add('is-invalid')
                } else {
                    this._input.classList.add('is-valid')
                    if (data.reload) {
                        window.location.reload();
                    }
                }

            })
    }
}, {extends: 'button'});
