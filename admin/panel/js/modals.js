/**
 * Creates a Modal on the page with the given title, content, buttons and callback when the modal is closed.
 * @constructor
 * @param {string} title The title of the modal placed at the header.
 * @param {string} content The content of the modal, can be in HTML.
 * @param {string} buttons The buttons of the modal placed at the footer, can be in HTML.
 * @param {function} callback The callback function when the modal is closed; it will return the event and the modal element.
 * 
 * @author Coppertine
 */
class modalPopup {
    constructor(title, content, buttons, callback = null) {
        this.callback = callback;
        this.modal = document.createElement('div');
        this.modal.classList.add('modal');
        this.modal.innerHTML = `
            <div class="modal__overlay"></div>
            <div class="modal__content">
                <div class="modal__header">
                    <h2 class="modal__title">${title}</h2>
                    <i class="fas fa-times modal__close-button"></i>
                </div>
                <div class="modal__body">
                    ${content}
                </div>
                <div class="modal__footer">
                    ${buttons}
                </div>
            </div>`;

        this.modal.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal__overlay') || e.target.classList.contains('modal__close-button') || e.target.classList.contains('button')) {
                this.close(e);
            }
        });

        document.body.appendChild(this.modal);
    }

    close(e) {
        if(this.callback) this.callback(e, this.modal);
        this.modal.classList.add('hidden');
        document.body.removeChild(this.modal);
    }
    
}
