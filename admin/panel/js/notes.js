class NotesElement extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: "open" });
        const link = Object.assign(document.createElement("link"), {
            rel: "stylesheet",
            href: "/admin/panel/css/notes.css",
        });
        this.shadowRoot.appendChild(link);


        // Splitting into 3 sections, Title, Content and Footer
        // Title should be a h1 element with the innerText of "Notes"
        // Content should be a div with the class of "notes__list"
        // Within Content, a div with the class of "notes__item" should be created for each note
        // Footer should be a div with the class of "notes__footer"
        // Within Footer, a textarea with the class of "notes__footer-input" should be created with a placeholder of "Note Here"
        // Within Footer, a div with the class of "notes__footer-info" is created, should contain an img with the class of "notes__footer-profile-picture", a span in the same line saying "posting as <strong>${username}</strong>" and a button with the class of "button" and the innerText of "Send"

        let titleSection = document.createElement("h1");
        titleSection.innerText = "Notes";
        let contentSection = Object.assign(document.createElement("div"), { className: "notes__list" });

        createExampleNotes(contentSection);

        let footerSection = document.createElement("footer");

        let footerInput = Object.assign(document.createElement("textarea"), { className: "input", placeholder: "Note Here" });
        let footerInfo = document.createElement("footer-info");
        let footerProfilePicture = Object.assign(document.createElement("img"), { className: "notes__footer-profile-picture", src: `https://a.ppy.sh/${oUserId}`, alt: "Profile Picture" });
        let footerInfoText = Object.assign(document.createElement("span"), { innerHTML: `posting as <strong>${oUsername}</strong>` });
        let footerButton = Object.assign(document.createElement("button"), { className: "button", innerText: "Send" });

        footerInfo.appendChild(footerProfilePicture);
        footerInfo.appendChild(footerInfoText);
        footerInfo.appendChild(footerButton);

        footerSection.appendChild(footerInput);
        footerSection.appendChild(footerInfo);

        this.shadowRoot.appendChild(titleSection);
        this.shadowRoot.appendChild(contentSection);
        this.shadowRoot.appendChild(footerSection);
    }
}

function createExampleNotes(contentSection) {
    const exampleNotes = [
        {
            userId: 18152711,
            username: "MegaMix_Craft",
            time: "2022-11-01T00:00:00",
            content: "Seems good to me!"
        },
        {
            userId: 10379965,
            username: "Tanza3D",
            time: "2022-11-01T02:00:00",
            content: "I added a few more examples in the solution, should read well"
        }
    ];

    exampleNotes.forEach(note => {
        let noteItem = document.createElement("comment-note");
        noteItem.setAttribute("userId", note.userId);
        noteItem.setAttribute("username", note.username);
        noteItem.setAttribute("time", note.time);
        noteItem.innerHTML = note.content;

        contentSection.appendChild(noteItem);
    });
}

/**
 *  Custom Note Element
 *  @class
 *  @extends HTMLElement
 *  @param {string} username - The username of the user who posted the note
 *  @param {number} userId - The ID of the user who posted the note
 *  @param {string} data - The content of the note
 */
class NoteElement extends HTMLElement {
    constructor() {
        super();

        let noteInformation = {
            noteUsername: this.getAttribute("username"),
            noteUserId: this.getAttribute("userId"),
            noteTime: this.getAttribute("time"),
            noteContent: this.innerHTML
        };

        this.attachShadow({ mode: "open" });

        this.shadowRoot.appendChild(Object.assign(document.createElement("link"), {
            rel: "stylesheet",
            href: "/admin/panel/css/notes.css",
        }));

        let profileImage = Object.assign(document.createElement("img"), { className: "notes__profile-picture", src: `https://a.ppy.sh/${noteInformation.noteUserId}`, alt: "Profile Picture" });
        let noteContent = Object.assign(document.createElement("div"), {
            className: "notes__content",
            innerHTML: `
                <span>
                    <user id="${noteInformation.noteUserId}">${noteInformation.noteUsername}</user> - ${TimeAgo.inWords(new Date(noteInformation.noteTime).getTime())}
                </span>
                <p>${noteInformation.noteContent}</p>`
        });
        
        this.shadowRoot.appendChild(profileImage);
        this.shadowRoot.appendChild(noteContent);
        createUserTippys();
    }
}

customElements.define("notes-section", NotesElement);
customElements.define("comment-note", NoteElement);