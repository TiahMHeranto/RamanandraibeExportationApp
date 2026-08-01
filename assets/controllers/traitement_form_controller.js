import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['list', 'row'];

    addLine() {
        const list = this.listTarget;
        const index = list.dataset.index;
        const prototype = list.dataset.prototype;
        const html = prototype.replace(/__name__/g, index);
        const wrapper = document.createElement('div');
        wrapper.className = 'line-row';
        wrapper.dataset.traitementFormTarget = 'row';
        wrapper.innerHTML = `<div>${html}</div><button type="button" class="btn btn-ghost" data-action="traitement-form#removeLine">Retirer</button>`;
        list.appendChild(wrapper);
        list.dataset.index = String(Number(index) + 1);
    }

    removeLine(event) {
        event.currentTarget.closest('.line-row')?.remove();
    }
}
