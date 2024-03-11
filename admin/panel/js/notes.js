class NotesElement extends HTMLElement {
    static get observedAttributes() {
        return ['note-id']
    }
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

        let footerSection = document.createElement("footer");

        let footerInput = Object.assign(document.createElement("textarea"), { className: "input", placeholder: "Note Here" });
        footerInput.addEventListener("keypress", function (e) {
            // Don't ask why, don't ask how, but for some FUCKED up reason, chrome treats the Enter part of CTRL+Enter as a fucking new line...
            // do not add any additonal spaces infront of line 35 or the end of line 34...
            if(e.ctrlKey && (e.key === `
` || e.key === "Enter"))
            {
                // this is where my comment should be, IF I HAD ONE
                submitNote();
            }
        });
        let footerInfo = document.createElement("footer-info");
        let footerProfilePicture = Object.assign(document.createElement("img"), { className: "notes__footer-profile-picture", src: `https://a.ppy.sh/${oUserId}`, alt: "Profile Picture" });
        let footerInfoText = Object.assign(document.createElement("span"), { innerHTML: `posting as <strong>${oUsername}</strong>` });
        let footerButton = Object.assign(document.createElement("button"), { className: "button", innerText: "Send"});
        footerButton.onclick = submitNote();

        footerInfo.appendChild(footerProfilePicture);
        footerInfo.appendChild(footerInfoText);
        footerInfo.appendChild(footerButton);

        footerSection.appendChild(footerInput);
        footerSection.appendChild(footerInfo);

        this.shadowRoot.appendChild(titleSection);
        this.shadowRoot.appendChild(contentSection);
        this.shadowRoot.appendChild(footerSection);
    }

    attributeChangedCallback(name, oldValue, newValue) {

        // Most likely a note id change, need to set and refresh notes.
        let notesPage = newValue;
        while (this.shadowRoot.children[2].lastElementChild) {
            this.shadowRoot.children[2].removeChild(this.shadowRoot.children[2].lastElementChild);
        }
        loadNotes(notesPage, this.shadowRoot.children[2]);
    }
}

// function createExampleNotes(contentSection) {
//     const exampleNotes = [
//         {
//             userId: 18152711,
//             username: "MegaMix_Craft",
//             time: "2022-11-01T00:00:00",
//             content: "Seems good to me!"
//         },
//         {
//             userId: 10379965,
//             username: "Tanza3D",
//             time: "2022-11-01T02:00:00",
//             content: "I added a few more examples in the solution, should read well"
//         }
//     ];

//     exampleNotes.forEach(note => {
//         let noteItem = document.createElement("comment-note", {
//             userid: note.userId,
//             username: note.username,
//             time: note.time
//         });
//         noteItem.innerHTML = note.content
//         contentSection.appendChild(noteItem);
//     });
// }

function loadNotes(id, contentSection) {
    getNotes(id, function () {
        let notes = JSON.parse(this.responseText);
        notes.forEach(note => {
            let noteItem = document.createElement("comment-note")
            noteItem.setAttribute("userid", note['Author']);
            noteItem.setAttribute("username", note['Username']);
            noteItem.setAttribute("time", note['Date']);
            noteItem.innerHTML = note['Text'].replace("\n", "<br/>");

            contentSection.appendChild(noteItem);
        })
    });
}

function getNotes(id, callback) {
    let xhr = new XMLHttpRequest();
    xhr.callback = callback;
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error retrieving medal."); };

    xhr.open("GET", "/admin/panel/api/base/notes/get?strPageId=" + id);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(null);
}

function submitNote()
{
    let notes_section = document.getElementsByTagName("notes-section")[0];
    if(notes_section.shadowRoot.children.length < 2)
        return;
    let messageString = notes_section.shadowRoot.children[3].children[0].value;
    if(messageString === "")
        return;
    let notePageId = notes_section.getAttribute("note-id");
    let notes_list = notes_section.shadowRoot.children[2];
    let xhr = createXHR("/admin/panel/api/base/notes/save");
    xhr.callback = function() {
        // off to "add" the note in..
        let noteItem = document.createElement("comment-note");
        noteItem.setAttribute("userid", oUserId);
        noteItem.setAttribute("username", oUsername);
        noteItem.setAttribute("time", new Date().toUTCString());
        noteItem.innerText = messageString.replace("\n", "<br/>");
        notes_list.appendChild(noteItem);
        notes_section.shadowRoot.children[3].children[0].value = "";
    };
    xhr.onload = function () { this.callback.apply(this); };
    xhr.onerror = function () { console.log("Error sending note."); };
    xhr.send(
        `strPageId=${notePageId}&strNoteContent=${messageString}`
    );
}


/**
 *  Custom Note Element
 *  @class
 *  @extends HTMLElement
 *  @param {string} username - The username of the user who posted the note
 *  @param {number} userId - The ID of the user who posted the note
 *  @param {string} date - The date of the note
 */
class NoteElement extends HTMLElement {
    static get observedAttributes() {
        return ['username', 'userid', 'time']
    }

    constructor() {
        super();

        let noteInformation = {
            noteUsername: this.getAttribute("username"),
            noteUserId: this.getAttribute("userid"),
            noteTime: this.getAttribute("time"),
            noteContent: this.innerText
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
                    <user id="${noteInformation.noteUserId}">${noteInformation.noteUsername}</user> - <span class="notes__time-tooltip">${TimeAgo.inWords(new Date(noteInformation.noteTime).getTime())}
                        <span class="notes__time-tooltip-text">${new Date(noteInformation.noteTime).toLocaleString()}</span></span>
                </span>
                <p>${noteInformation.noteContent}</p>`
        });

        this.shadowRoot.appendChild(profileImage);
        this.shadowRoot.appendChild(noteContent);

        this.addEventListener('DOMSubtreeModified', function (e) {
            if (e.target.innerText != "" && e.target.innerText != null) {
                e.target.shadowRoot.children[2].children[1].innerHTML = `${e.target.innerText.replace("\n", "<br/>")}`;
            }
        });
        createUserTippys();
    }

    attributeChangedCallback(name, oldValue, newValue) {
        switch (name) {
            case 'userid':
                this.shadowRoot.children[1].src = `https://a.ppy.sh/${newValue}`; // image.
                this.shadowRoot.children[2].children[0].children[0].id = `${newValue}`;
                break;
            case 'username':
                this.shadowRoot.children[2].children[0].children[0].innerText = `${newValue}`;
                break;
            case 'time':
                this.shadowRoot.children[2].children[0].children[1].innerHTML = `${TimeAgo.inWords(new Date(newValue).getTime())}<span class="notes__time-tooltip-text">${new Date(newValue).toLocaleString()}</span>`;
                break;
        }
    }
}

customElements.define("notes-section", NotesElement);
customElements.define("comment-note", NoteElement);