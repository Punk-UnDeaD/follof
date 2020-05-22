import debounce from 'debounce';

customElements.define('x-autocomplete', class extends HTMLInputElement {
    onChange() {
        if (!this.value) return;
        console.log('change')
        fetch(this.dataset.url + '/' + this.value,
            {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then((resp) => resp.json())
            .then((resp) => this.doDrop(resp.values))

    }

    connectedCallback() {
        const listener = debounce(() => this.onChange(), 333);
        this.addEventListener('keydown', listener);
        this.addEventListener('focus', listener);
        this.addEventListener('keyup', listener);
        this.addEventListener('blur', () => this.hideDrop());
    }

    doDrop(values) {
        if (!values.length) {
            this.hideDrop();
            return
        }
        const dropDown = this._dropDown || this.newDropElem();
        dropDown.innerHTML = `<ul>${
            values.map(e => `<li>${e.replace(this.value, `<b>${this.value}</b>`)}</li>`).join('')
        }</ul>`;
        this._dropDown = dropDown;
    }

    hideDrop() {
        setTimeout(() => {
            if (this._dropDown) {
                this._dropDown.remove();
                this._dropDown = null;
            }
        }, 200);
    }

    newDropElem() {
        const dropDown = document.createElement("div");
        const rect = this.getBoundingClientRect();
        dropDown.classList.add('x-autocomplete-drop')
        dropDown.style.position = "absolute";
        dropDown.style.top = rect.height + rect.top + "px";
        dropDown.style.left = rect.left + "px";
        dropDown.style.width = this.offsetWidth - 2 + "px";
        document.body.appendChild(dropDown);
        dropDown.addEventListener('click', (e) => {
            let li = e.target;
            console.log(li, '1')
            if (li.nodeName !== 'LI')
                li = li.closest('li')
            console.log(li, '2')
            if (!li) return;
            this.value = li.innerText;

        })
        return dropDown;
    }

}, {extends: 'input'});
