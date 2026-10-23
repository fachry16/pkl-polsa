import './bootstrap';

import Alpine from 'alpinejs';

window.searchableSelect = function (items, initial, settings = {}) {
    return {
        items,
        query: '',
        open: false,
        active: 0,
        selected: null,
        autoSubmit: !!settings.autoSubmit,
        required: !!settings.required,

        init() {
            const value = initial === null || initial === undefined ? '' : String(initial);
            if (value !== '') {
                this.selected = this.items.find(item => String(item.value) === value) ?? null;
            }
            this.query = this.selected?.label ?? '';

            if (this.required) {
                this.$root.closest('form')?.addEventListener('submit', (event) => {
                    if (this.selected || event.defaultPrevented) return;
                    event.preventDefault();
                    this.open = true;
                    this.$nextTick(() => this.$el.querySelector('input[type="text"]')?.focus());
                });
            }
        },

        get filtered() {
            const q = this.query.trim().toLowerCase();
            if (!q || q === this.selected?.label.toLowerCase()) return this.items;
            return this.items.filter(item => item.search.includes(q));
        },

        onFocus() {
            this.open = true;
            this.active = 0;
        },

        onInput() {
            this.open = true;
            this.active = 0;
            if (this.selected && this.query !== this.selected.label) this.selected = null;
        },

        move(step) {
            if (!this.open) {
                this.open = true;
                return;
            }
            const max = this.filtered.length - 1;
            this.active = Math.max(0, Math.min(max, this.active + step));
            this.$nextTick(() => this.$el.querySelector('.is-active')?.scrollIntoView({ block: 'nearest' }));
        },

        chooseActive() {
            const item = this.filtered[this.active];
            if (item) return this.choose(item);
            this.open = false;
            this.submitIfNeeded();
        },

        choose(item) {
            if (item.value === '') {
                this.clear(false);
                this.submitIfNeeded();
                return;
            }
            this.selected = item;
            this.query = item.label;
            this.open = false;
            this.submitIfNeeded();
        },

        clear(submit = true) {
            this.selected = null;
            this.query = '';
            this.open = false;
            if (submit) this.submitIfNeeded();
        },

        close() {
            this.open = false;
            if (!this.selected) this.query = '';
        },

        submitIfNeeded() {
            if (!this.autoSubmit) return;
            this.$nextTick(() => this.$root.closest('form')?.requestSubmit());
        },
    };
};

window.Alpine = Alpine;

Alpine.start();
