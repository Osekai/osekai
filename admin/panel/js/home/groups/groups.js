class roleTag extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        const wrapper = Object.assign(document.createElement('div'), {
            className: 'role-button-wrapper'
        });
        const removeButton = Object.assign(document.createElement('div'), {
            className: 'role-button remove',
            style: `color: ${this.getAttribute('color')};`
        });
        
        const role = Object.assign(document.createElement('div'), {
            className: 'role-tag-role',
            innerText: this.getAttribute('role').toUpperCase(),
            style: `background-color: ${this.getAttribute('color')};`,
        });
        const link = Object.assign(document.createElement("link"), {
            rel: "stylesheet",
            href: "/admin/panel/css/home/groups/groups.css",
        });

        this.shadowRoot.appendChild(link);
        wrapper.appendChild(removeButton);
        wrapper.appendChild(role);
        this.shadowRoot.appendChild(wrapper);
    }
}

customElements.define('user-role', roleTag);

createUserTippys();