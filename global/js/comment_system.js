// import xhr.js before
const API_URL_COMMENTS = '/global/api/comment_system.php';
const REQUIRED_KEY_COMMENTS = 'bGetComments=true';
const parser = new DOMParser();


var COMMENTS_col_medals = [];
var COMMENTS_col_hash = [];
var COMMENTS_boxes = [];
var COMMENTS_mode = 1;
var COMMENTS_type = 1;

window.addEventListener('click', function (e) {
    if (!document.getElementById('filter__button').contains(e.target)) document.getElementById("filter__list").classList.add("osekai__dropdown-hidden");
});

window.addEventListener('DOMContentLoaded', (event) => {
    if (mobile) {
        document.getElementById("comments-panel").classList.add("osekai__panel-collapsable");
        document.getElementById("comments-panel").classList.add("osekai__panel-collapsable-collapsed");
        document.getElementById("comments-panel").querySelector(".osekai__panel-header-with-buttons").querySelector(".osekai__panel-hwb-left").innerHTML += `<div class="osekai__panel-header-right">
    <i class="fas fa-chevron-down" aria-hidden="true"></i>
</div>`;
    }
});

async function Comments_Require(MedalID, oParent, bReload = false, VersionId = -1, ProfileId = -1) {
    if (VersionId !== -1) {
        MedalID = VersionId;
        COMMENTS_type = 3;
    }
    if (ProfileId !== -1) {
        MedalID = ProfileId;
        COMMENTS_type = 4;
    }
    if (COMMENTS_boxes[MedalID] && !bReload) {
        Comments_Sort(oParent, MedalID);
        return;
    }

    COMMENTS_col_medals[MedalID] = [];
    COMMENTS_col_hash = [];
    COMMENTS_boxes[MedalID] = [];
    oParent.innerHTML = loader;

    console.log("[Locale] loading comments locale");
    await loadSource("comments");
    console.log("[Locale] loaded comments locale");

    var xhr = createXHR(API_URL_COMMENTS);
    if (VersionId !== -1) {
        xhr.send(REQUIRED_KEY_COMMENTS + "&nVersionId=" + VersionId);
    } else if (ProfileId !== -1) {
        xhr.send(REQUIRED_KEY_COMMENTS + "&nProfileId=" + ProfileId);
    } else {
        console.log(MedalID);
        xhr.send(REQUIRED_KEY_COMMENTS + "&strMedalID=" + MedalID);
    }
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) {
            oParent.innerHTML = "";
            return;
        }
        console.log(oResponse);
        Object.keys(oResponse).forEach(function (obj) {
            if (oResponse[obj] !== null && oResponse[obj].MedalID == MedalID) COMMENTS_col_medals[MedalID].push(oResponse[obj]);
        });
        Comments_Sort(oParent, MedalID);
        tippy("[data-tippy-content-comment-date]", {
            content: function (reference) {
                return reference.getAttribute("data-tippy-content-comment-date");
            }
        });
    };

    if (document.getElementById("comments__emoji_container")) {
        var emoji = document.getElementById("comments__emoji_container");
        const picker = picmo.createPicker({
            rootElement: emoji,
            className: 'osekai__emoji-picker',
            autoFocus: 'none'
        });
        picker.addEventListener('emoji:select', selection => {
            document.getElementById("comments__input").value += selection.emoji;
        });
    }

};

function Comments_HierarchySort(hashArr, key, result, MedalID) {
    if (hashArr[key] == undefined) return;
    var arr = hashArr[key].sort((a, b) => {
        if (a.Pinned == 1) return -1;
        if (b.Pinned == 1) return 1;
        if (COMMENTS_mode == 1) {
            if (parseInt(a.VoteSum ?? 0) > parseInt(b.VoteSum ?? 0)) return -1;
            if (parseInt(a.VoteSum ?? 0) < parseInt(b.VoteSum ?? 0)) return 1;
            return a.PostDate < b.PostDate ? 1 : -1;
        } else if (COMMENTS_mode == 2) {
            return a.PostDate < b.PostDate ? 1 : -1;
        }
    });
    for (var i = 0; i < arr.length; i++) {
        result.push(arr[i]);
        Comments_HierarchySort(hashArr, arr[i].ID, result);
    }

    COMMENTS_col_medals[MedalID] = result;
}

function Comments_Sort(oParent, MedalID, VersionId = -1, ProfileID = -1) {
    if (ProfileID !== -1) MedalID = ProfileID;
    if (VersionId !== -1) MedalID = VersionId;
    if (COMMENTS_col_medals[MedalID] == undefined) return;
    console.log(MedalID);
    COMMENTS_boxes[MedalID] = [];
    COMMENTS_col_hash = [];

    for (var i = 0; i < COMMENTS_col_medals[MedalID].length; i++) {
        if (COMMENTS_col_medals[MedalID][i].MedalID.toString() !== MedalID.toString()) return;
        if (COMMENTS_col_hash[COMMENTS_col_medals[MedalID][i].Parent] == undefined) COMMENTS_col_hash[COMMENTS_col_medals[MedalID][i].Parent] = [];
        COMMENTS_col_hash[COMMENTS_col_medals[MedalID][i].Parent].push(COMMENTS_col_medals[MedalID][i]);
    }

    Comments_HierarchySort(COMMENTS_col_hash, 0, [], MedalID);
    Comments_Create(oParent, MedalID);
    twemoji.parse(oParent);
}

function generateComment(commentdata) {
    console.log(commentdata);
    var comment = Object.assign(document.createElement("div"), { className: "comments__comment" });
    if (commentdata.Pinned) {
        comment.classList.add("comments__comment-pinned");
    }
    if (commentdata.ParentCommenter)
        comment.classList.add("comments__comment-reply");

    var comment_left = Object.assign(document.createElement("div"), { className: "comments__comment-left" });

    var comment_left_pfp = Object.assign(document.createElement("img"), { className: "comments__pfp", src: commentdata.AvatarURL });
    var comment_left_votes = Object.assign(document.createElement("div"), { className: "comments__comment-votes", innerText: "+" + (commentdata.VoteSum != null ? commentdata.VoteSum : "0") });
    if (bLoggedIn) {
        if (commentdata.HasVoted) {
            comment_left_votes.classList.add("comments__comment-votes-voted");
        }
        comment_left_votes.addEventListener("click", function () {
            console.log("clicked :D");
            voteComment(commentdata.ID, comment_left_votes);
        })
    }
    comment_left.appendChild(comment_left_pfp);
    comment_left.appendChild(comment_left_votes);

    var comment_right = Object.assign(document.createElement("div"), { className: "comments__comment-right" });
    var comment_right_content = Object.assign(document.createElement("div"), { className: "comments__comment-content" });
    var comment_right_infobar = Object.assign(document.createElement("div"), { className: "comments__comment-infobar" });


    comment_right.appendChild(comment_right_content);
    comment_right.appendChild(comment_right_infobar);

    var comment_right_content_username = Object.assign(document.createElement("div"), { className: "comments__comment-content-username" });
    comment_right_content.appendChild(comment_right_content_username);


    var comment_right_content_username_roles = groupUtils.badgeHtmlFromCommaSeperatedList(commentdata['Groups'], "small", 2);
    var comment_right_content_username_username = Object.assign(document.createElement("a"), { className: "comments__username", innerText: commentdata.Username, href: "https://osekai.net/profiles?user=" + commentdata.UserID });
    comment_right_content_username.appendChild(comment_right_content_username_username);
    comment_right_content_username.innerHTML += comment_right_content_username_roles;

    var comment_right_content_username_right = Object.assign(document.createElement("div"), { className: "comments__comment-content-username-right" });
    comment_right_content_username.appendChild(comment_right_content_username_right);

    if (commentdata.Pinned == 1) {
        var comment_right_content_username_right_pinned = Object.assign(document.createElement("div"), { className: "comments__comment-content-pinned" });
        var comment_right_content_username_right_pinned_text = Object.assign(document.createElement("p"), { innerText: "Pinned" });
        var comment_right_content_username_right_pinned_icon_container = Object.assign(document.createElement("div"), { className: "comments__comment-content-pinned-icon" });
        var comment_right_content_username_right_pinned_icon = Object.assign(document.createElement("i"), { className: "oif-pin" });
        comment_right_content_username_right_pinned_icon_container.appendChild(comment_right_content_username_right_pinned_icon);
        comment_right_content_username_right_pinned.appendChild(comment_right_content_username_right_pinned_text);
        comment_right_content_username_right_pinned.appendChild(comment_right_content_username_right_pinned_icon_container);
        comment_right_content_username_right.appendChild(comment_right_content_username_right_pinned);
    }

    var comment_right_content_text = Object.assign(document.createElement("div"), { className: "comments__comment-content-text" });

    var userText = BBCodeParser.process(parser.parseFromString(commentdata.PostText, "text/html").body.textContent).replaceAll(new RegExp(/(?<!=")(\b[\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/, 'g'), "<a href='$1' target='_blank'>$1</a>");
    var comment_right_content_text_p = Object.assign(document.createElement("p"), { innerHTML: userText });
    comment_right_content_text.appendChild(comment_right_content_text_p);
    comment_right_content.appendChild(comment_right_content_text);

    // infobar
    function createInfobarElement(icon, html, tooltip = "") {
        element = Object.assign(document.createElement("div"), { className: "comments__comment-infobar-info" });
        element_icon = Object.assign(document.createElement("i"), { className: icon });
        element_text = Object.assign(document.createElement("p"), { innerHTML: html });
        if (tooltip != "") {
            element.classList.add("tooltip-v2");
            element.setAttribute("tooltip-content", tooltip);
        }
        element.appendChild(element_icon);
        element.appendChild(element_text);
        return element;
    }
    function createInfobarButton(icon, size, callback) {
        element = Object.assign(document.createElement("div"), { className: "osekai__dropdown-opener comments__comment-infobar-button comments__comment-infobar-button-" + size });
        element_icon = Object.assign(document.createElement("i"), { className: icon });
        element.appendChild(element_icon);
        element.addEventListener("click", callback);
        return element;
    }
    var date = GetStringRawNonAsync("comments", "posted", [TimeAgo.inWords(new Date(commentdata.PostDate).getTime())]);
    comment_right_infobar.appendChild(createInfobarElement("fas fa-calendar-alt", date, new Date(commentdata.PostDate).toUTCString()));
    if (commentdata.ParentCommenter)
        comment_right_infobar.appendChild(createInfobarElement("fas fa-reply", GetStringRawNonAsync("comments", "replyingTo", [commentdata.ParentCommenter])));

    var comment_right_infobar_right = Object.assign(document.createElement("div"), { className: "comments__comment-infobar-right" });
    comment_right_infobar.appendChild(comment_right_infobar_right);
    comment_right_infobar_right.appendChild(createInfobarButton("fas fa-reply", "big", function () {
        openReply(commentdata.ID, comment);
    }));
    let eldropdown = Object.assign(document.createElement("div"), { "className": "osekai__dropdown osekai__dropdown-hidden" });
    function dropdownItem(name, callback, _class = "") {
        var item = Object.assign(document.createElement("div"), { "className": "osekai__dropdown-item " + _class, "innerHTML": name });
        item.addEventListener("click", callback);
        return item;
    }
    eldropdown.appendChild(dropdownItem(`<i class="fas fa-exclamation-triangle"></i> Report`, function () {
        eldropdown.classList.toggle("osekai__dropdown-hidden");
        doReport('comment', commentdata.ID);
    }, "osekai__dropdown-item-red"))
    if (bLoggedIn) {
        if (nRights > 0 || (nUserId == commentdata.MedalID && nAppId == "3")) {
            eldropdown.appendChild(dropdownItem(`<i class="fas fa-exclamation-triangle"></i> Delete`, function () {
                deleteComment(commentdata.ID);
                eldropdown.classList.toggle("osekai__dropdown-hidden");
            }, "osekai__dropdown-item-red"))
            var name = "Pin";
            if (commentdata.ParentCommenter) name = "Highlight";
            var alreadyPinned = false;
            if (commentdata.Pinned == 1) {
                alreadyPinned = true;
                var name = "Unpin";
                if (commentdata.ParentCommenter) name = "Un-highlight";
            }
            eldropdown.appendChild(dropdownItem(name, function () {
                pinComment(commentdata.ID, alreadyPinned);
                eldropdown.classList.toggle("osekai__dropdown-hidden");
            }))
        }
    }
    comment_right_infobar_right.appendChild(createInfobarButton("fas fa-ellipsis-h", "small", function () {
        console.log("opening dropdown");
        eldropdown.classList.remove("osekai__dropdown-hidden");
    }));
    comment_right_infobar_right.appendChild(eldropdown);

    comment.appendChild(comment_left);
    comment.appendChild(comment_right);

    return comment;
}

function Comments_Create(oParent, MedalID) {
    let nOrder = 0;
    var inPinned = false;
    for (let oComment of COMMENTS_col_medals[MedalID]) {
        if (oComment == null || oComment.MedalID.toString() !== MedalID.toString()) return;

        if (oComment.ParentCommenter == null && inPinned) {
            inPinned = false;
            COMMENTS_boxes[MedalID].push(Object.assign(document.createElement("div"), { "classList": "osekai__divider" }));
        }

        let oBox = generateComment(oComment);
        oBox.classList.add("comments__comment");
        oBox.setAttribute("CommentID", oComment.ID);
        oBox.id = "comment#" + nOrder;
        nOrder += 1;
        oBox.setAttribute("CommentCreator", oComment.Username);

        COMMENTS_boxes[MedalID].push(oBox);
        if (oComment.Pinned == 1) {
            inPinned = true;
        }
    }
    if (Object.keys(COMMENTS_col_medals[MedalID]).length <= Object.keys(COMMENTS_boxes[MedalID]).length) Comments_Out(oParent, MedalID);
    else console.log("oh no");
}

function commentsSendClick(nVersionID = -1, nProfileId = -1) {
    Comments_CloseEmojiPopup();
    let strComment = document.getElementById("comments__input").value;

    if (strComment.includes("https://osu.ppy.sh/beatmapsets/")) {
        openDialog("Are you sure you want to post this comment?", "Your reply looks like it contains an osu! beatmap URL.", "If you're trying to post a beatmap, you should instead use the 'Add' button on the beatmaps panel.", [
            {
                "text": "Send Anyway",
                "callback": function () {
                    newComment(strComment, nVersionID, nProfileId);
                    document.getElementById("comments__input").value = "";
                },
                "highlighted": true,
            },
            {
                "text": "Cancel",
                "callback": function () { },
                "highlighted": false,
            }
        ]);
    } else {
        newComment(strComment, nVersionID, nProfileId);
        document.getElementById("comments__input").value = "";
    }
}

function newComment(strText, nVersionId = -1, nProfileId = -1) {
    if (restrictedState == 1) return;
    if (strText.replace(" ", "").replace(/\s/g, '') == "") return;
    //strText = strText.replace("&", "&amp;");
    strText = strText.replace(/&/g, encodeURIComponent("&"));
    var xhr = createXHR(API_URL_COMMENTS);
    if (nVersionId !== -1) {
        xhr.send("strComment=" + strText + "&nVersionId=" + nVersionId);
    } else if (nProfileId !== -1) {
        xhr.send("strComment=" + strText + "&nProfileId=" + nProfileId);
    } else {
        xhr.send("strComment=" + strText + "&strCommentMedalID=" + nCurrentMedalID);
    }
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            RequireComments();
        }
    }
}

function voteComment(nID, element) {
    var xhr = createXHR(API_URL_COMMENTS);
    xhr.send("nObject=" + nID + "&nType=" + COMMENTS_type);

    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        Object.keys(oResponse).forEach(function (obj) {
            if (oResponse[obj].HasVoted == 1) {
                element.innerHTML = "+" + (parseInt(element.innerHTML) - 1);
            } else {
                element.innerHTML = "+" + (parseInt(element.innerHTML) + 1);
            }
            element.classList.toggle("comments__comment-votes-voted");
        });
    };
}

function openReply(strCommentId, element) {
    if (handleUndefined(element.nextSibling) || !(element.nextSibling.id == "oReplyBox")) {
        if (handleUndefined(document.getElementById("oReplyBox")) == false) document.getElementById("oReplyBox").remove();
        let oReferenceNode = element;
        let oReplyBox = document.createElement("div");
        oReplyBox.id = "oReplyBox";

        oReplyBox.innerHTML = document.getElementById("comments__post_area").innerHTML.replace("comments__send", "reply__send").replace("comments__input", "reply__input").replace("comments__input-box__emoji", "hidden");
        // emojis don't work in replies :D

        handleUndefined(element.nextSibling) ? oReferenceNode.parentNode.insertBefore(oReplyBox, null) : oReferenceNode.parentNode.insertBefore(oReplyBox, oReferenceNode.nextSibling);

        document.getElementById("reply__send").addEventListener("click", () => {
            if (document.getElementById("reply__input").value.includes("https://osu.ppy.sh/beatmapsets/")) {
                openDialog("Are you sure you want to post this comment?", "Your reply looks like it contains an osu! beatmap URL.", "If you're trying to post a beatmap, you should instead use the 'Add' button on the beatmaps panel.", [
                    {
                        "text": "Send Anyway",
                        "callback": function () {
                            replySend();
                        },
                        "highlighted": true,
                    },
                    {
                        "text": "Cancel",
                        "callback": function () { },
                        "highlighted": false,
                    }
                ]);
            } else {
                replySend();
            }
        });
    } else {
        if (handleUndefined(document.getElementById("oReplyBox")) == false) document.getElementById("oReplyBox").remove();
    }
}

function replySend() {
    Comments_CloseEmojiPopup();
    let oReplyBox = document.getElementById("oReplyBox");
    console.log(oReplyBox.previousSibling.outerHTML);
    newReply(document.getElementById("reply__input").value, oReplyBox.previousSibling.getAttribute("CommentID"), oReplyBox.previousSibling.getAttribute("CommentCreator"));
    document.getElementById("reply__input").value = "";
}

function newReply(strText, nParentID, strParentCommenter) {
    Comments_CloseEmojiPopup();
    var xhr = createXHR(API_URL_COMMENTS);
    if (COMMENTS_type == 1) {
        xhr.send("strComment=" + strText + "&strCommentMedalID=" + nCurrentMedalID + "&nParentComment=" + nParentID + "&strParentCommenter=" + strParentCommenter);
    } else if (COMMENTS_type == 3) {
        xhr.send("strComment=" + strText + "&nVersionId=" + parseInt(data[currentindex]['version_info']['id']) + "&nParentComment=" + nParentID + "&strParentCommenter=" + strParentCommenter);
    } else if (COMMENTS_type == 4) {
        xhr.send("strComment=" + strText + "&nProfileId=" + new URLSearchParams(window.location.search).get("user") + "&nParentComment=" + nParentID + "&strParentCommenter=" + strParentCommenter);
    }
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            RequireComments();
        }
    }
}

function deleteComment(nID) {
    if (!confirm("Are you sure you want to delete this comment?")) return;
    var xhr = createXHR(API_URL_COMMENTS);
    xhr.send("nCommentDeletion=" + nID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            RequireComments();
        }
    }
}

function pinComment(nID, alreadyPinned) {
    if (alreadyPinned) {
        // TODO: make this an actually nice popup, likewise with deletion
        if (!confirm("Are you sure you want to pin this comment?")) return;
    } else {
        if (!confirm("Are you sure you want to unpin this comment?")) return;
    }
    var xhr = createXHR(API_URL_COMMENTS);
    xhr.send("nCommentPin=" + nID);
    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        if (oResponse.toString() == "Success!") {
            RequireComments();
        }
    }
}

function textAreaAdjust(o) {
    //o.style.height = "1px";
    o.style.height = (o.scrollHeight) + "px";
}

function urlify(text) {
    var urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function (url) {
        return '<a href="' + url + '">' + url + '</a>';
    })
}

function RequireComments() {
    if (COMMENTS_type == 1) {
        Comments_Require(nCurrentMedalID, document.getElementById("comments__box"), true);
    } else if (COMMENTS_type == 4) {
        Comments_Require("", document.getElementById("comments__box"), true, -1, new URLSearchParams(window.location.search).get("user"));
    } else {
        Comments_Require("", document.getElementById("comments__box"), true, parseInt(data[currentindex]['version_info']['id']));
    }
}

function Comments_Out(oParent, MedalID) {
    COMMENTS_boxes[MedalID].forEach((oBox) => {
        oParent.appendChild(oBox);
    });
}

function Comments_OpenEmojiPopup() {
    document.getElementById("comments__emoji_container").classList.toggle("comments_emoji_popup_conainer-hidden");
}

function Comments_CloseEmojiPopup() {
    if (document.getElementById("comments__emoji_container")) {
        document.getElementById("comments__emoji_container").classList.add("comments_emoji_popup_conainer-hidden");
    }
}