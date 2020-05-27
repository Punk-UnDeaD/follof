'use strict';

const _createClass = function () {
    function defineProperties(target, props) {
        for (let i = 0; i < props.length; i++) {
            const descriptor = props[i];
            descriptor.enumerable = descriptor.enumerable || false;
            descriptor.configurable = true;
            if ("value" in descriptor) descriptor.writable = true;
            Object.defineProperty(target, descriptor.key, descriptor);
        }
    }

    return function (Constructor, protoProps, staticProps) {
        if (protoProps) defineProperties(Constructor.prototype, protoProps);
        if (staticProps) defineProperties(Constructor, staticProps);
        return Constructor;
    };
}();

Object.defineProperty(exports, "__esModule", {
    value: true
});

function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
        throw new TypeError("Cannot call a class as a function");
    }
}

const div = function div(className) {
    const node = document.createElement('div');

    node.classList.add(className);
    return node;
};

const defaultOffset = 12;
const positions = ['top', 'right', 'bottom'];

const Toolpip = function () {
    function Toolpip() {
        const _this = this;

        const rootNode = arguments.length <= 0 || arguments[0] === undefined ? null : arguments[0];
        const options = arguments.length <= 1 || arguments[1] === undefined ? {} : arguments[1];

        _classCallCheck(this, Toolpip);

        this.type = options.type || 'default';

        this.title = options.title;
        this.subtitle = options.subtitle;

        this.shown = false;

        this.toolpip = this.createToolpip();
        this.toolpip.addEventListener('click', function (event) {
            _this.hide();
            event.stopPropagation();
        });

        if (rootNode) {
            this.position(rootNode);
        }
    }

    _createClass(Toolpip, [{
        key: 'position',
        value: function position(target) {
            const _this2 = this;

            const _position = arguments.length <= 1 || arguments[1] === undefined ? 'top' : arguments[1];

            const rects = target.getBoundingClientRect();
            const bodyTop = Math.abs(document.body.getBoundingClientRect().top);
            this.positionClass = _position;
            this.target = target;
            positions.forEach(function (className) {
                return _this2.toolpip.classList.remove(className);
            });
            if ('top' === _position) {
                this.toolpip.style.left = rects.left - this.toolpip.clientWidth / 2 + rects.width / 2 + 'px';
                this.toolpip.style.top = rects.top + bodyTop - defaultOffset - this.toolpip.clientHeight + 'px';
            }

            if ('right' === _position) {
                this.toolpip.style.left = rects.left + rects.width + 'px';
                this.toolpip.style.top = rects.top + bodyTop + 'px';
            }

            if ('bottom' === _position) {
                this.toolpip.style.left = rects.left - this.toolpip.clientWidth / 2 + rects.width / 2 + 'px';
                this.toolpip.style.top = rects.bottom + bodyTop - defaultOffset + 'px';
            }

            this.toolpip.classList.add(this.positionClass);
        }
    }, {
        key: 'createToolpip',
        value: function createToolpip() {
            const _this3 = this;

            const toolpip = div('toolpip');
            const title = div('toolpip-title');
            const subtitle = div('toolpip-subtitle');

            if (this.type) {
                toolpip.classList.add(this.type);
            }

            const updateContent = this.updateContent = function () {
                const titleText = arguments.length <= 0 || arguments[0] === undefined ? _this3.title : arguments[0];
                const subtitleText = arguments.length <= 1 || arguments[1] === undefined ? _this3.subtitle : arguments[1];

                title.textContent = titleText;
                subtitle.textContent = subtitleText;
            };

            updateContent();

            [title, subtitle].forEach(function (el) {
                return toolpip.appendChild(el);
            });
            document.body.appendChild(toolpip);

            return toolpip;
        }
    }, {
        key: 'hide',
        value: function hide() {
            this.toolpip.classList.remove('shown');
            this.shown = false;
        }
    }, {
        key: 'show',
        value: function show() {
            this.toolpip.classList.add('shown');
            this.shown = true;
        }
    }]);

    return Toolpip;
}();

exports.default = Toolpip;
