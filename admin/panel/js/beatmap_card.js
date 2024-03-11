let currentBeatmap = {};
class BeatmapCard extends HTMLElement {
    static get observedAttributes() {
        return ['beatmap-id', 'osekai-id', 'report-id']
    }
    constructor() {
        super();

        this.attachShadow({ mode: "open" });
        const link = Object.assign(document.createElement("link"), {
            rel: "stylesheet",
            href: "/admin/panel/css/beatmap_card.css",
        });
        this.shadowRoot.appendChild(link);

        let beatmapCardContainer = Object.assign(document.createElement("div"), {
            className: "beatmap-card--container"
        });
        beatmapCardContainer.style = "background:radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%),url(https://assets.ppy.sh/beatmaps/1830144/covers/cover@2x.jpg),linear-gradient(#240d19,#240d19)";

        let beatmapTopSection = Object.assign(document.createElement("div"), {
            className: "beatmap-card--header" 
        })
        let beatmapTitle = Object.assign(document.createElement("h3"), {
            innerText: "Griffin Lewis - Princess of the Night"
        });
        let beatmapDifficulty = Object.assign(document.createElement("span"), {
            innerText: "[Irre's Extra]"
        });
        
        beatmapTopSection.appendChild(beatmapTitle);
        beatmapTopSection.appendChild(beatmapDifficulty);

        let beatmapFooter = Object.assign(document.createElement("div"), {
            className: "beatmap-card--footer"
        });
        let beatmapMapper = Object.assign(document.createElement("span"), {
            innerHTML: "mapped by <a>Irreversible</a>"
        });
        let beatmapStats = Object.assign(document.createElement("span"),
        {
            innerText: `154bpm ${3.13.toLocaleString()}*`
        });
        beatmapFooter.appendChild(beatmapMapper);
        beatmapFooter.appendChild(beatmapStats);
        beatmapCardContainer.appendChild(beatmapTopSection);
        beatmapCardContainer.appendChild(beatmapFooter);

        this.shadowRoot.appendChild(beatmapCardContainer);
    }



    attributeChangedCallback(name, oldValue, newValue) {
        switch (name) {
            case 'beatmap-id':
                updateBeatmapMap(newValue, this);
                break;
            case 'osekai-id':
                updateOsekaiMap(newValue, this);
                break;
            case 'report-id':
                updateReportMap(newValue, this);
                break;
            default:
                break;
        }
    }

}

customElements.define("beatmap-card", BeatmapCard);

function updateBeatmapMap(beatmapId, el) {
    var xhr = createXHR("/admin/panel/api/beatmaps/beatmap");
    xhr.send(`numBeatmapSet=${beatmapId}`)
    xhr.onload = function () {
        updateBeatmapCard(JSON.parse(this.responseText)[0], el);
    }
}

function updateBeatmapCard(beatmapInfo, el) {
    currentBeatmap = beatmapInfo;
    const shadowRoot = el.shadowRoot;
    
    shadowRoot.children[1].style = `background: radial-gradient(50% 50% at 50% 50%, rgba(0, 0, 0, 0.2) 0%, rgba(0, 0, 0, 0.4) 100%),url(https://assets.ppy.sh/beatmaps/${beatmapInfo.MapsetID}/covers/cover.jpg),linear-gradient(#240d19,#240d19);`;
    shadowRoot.children[1].children[0].children[0].innerHTML = `<a href="https://osu.ppy.sh/b/${beatmapInfo.BeatmapID}">${beatmapInfo.SongTitle}</a>`;

    
    shadowRoot.children[1].children[0].children[1].innerText = beatmapInfo.Artist;

    shadowRoot.children[1].children[1].children[0].innerHTML = `mapped by <a href="https://osu.ppy.sh/u/${beatmapInfo.MapperID}">${beatmapInfo.Mapper}</a>`;
    shadowRoot.children[1].children[1].children[1].innerText = `${beatmapInfo.bpm}bpm ${beatmapInfo.Difficulty.toLocaleString()}*`;

    // Ignore this scuffness.. this needs to be somewhere else to listen to these edits above..
    document.querySelector(".report__beatmap-submitter-id").children[0].innerText = currentBeatmap.SubmittedBy;
    document.querySelector(".report__beatmap-submitter-username").children[0].innerText = currentBeatmap.SubmittedUsername;
}