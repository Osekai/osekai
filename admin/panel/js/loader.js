/**
 * Creates a Loading Spinner into the given element.
 * Note: This class will add .spinner-container to the element.
 * Please remove it when you're done.
 * @author Coppertine
 */
class loadingSpinner {
    constructor(div) {
        div.classList.add("spinner-container");
        this.spinner = document.createElement('svg');
        this.spinner.classList.add('spinner');
        this.spinner.innerHTML = `<circle class="ring" cx="25" cy="25" r="22.5"></circle><circle class="line" cx="25" cy="25" r="22.5"></circle>`;
        this.spinner.viewBox = "0 0 50 50";
        div.appendChild(this.spinner);
    }
}
