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
    oParent.innerHTML = "<div class='osekai__replace__loader'><svg viewBox='0 0 50 50' class='spinner'><circle class='ring' cx='25' cy='25' r='22.5' /><circle class='line' cx='25' cy='25' r='22.5' /></svg></div>"

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

function Comments_Create(oParent, MedalID) {
    let nOrder = 0;
    COMMENTS_col_medals[MedalID].forEach(oComment => {
        if (oComment == null || oComment.MedalID.toString() !== MedalID.toString()) return;
        let oBox = document.createElement("div");
        oBox.classList.add("comments__comment");
        oBox.setAttribute("CommentID", oComment.ID);
        oBox.id = "comment#" + nOrder;
        nOrder += 1;
        if (oComment.ParentCommenter) oBox.classList.add("comments__reply");
        oBox.setAttribute("CommentCreator", oComment.Username);

        var rolehtml = groupUtils.badgeHtmlFromCommaSeperatedList(oComment['Groups'], "small", 2);

        var postedText = GetStringRawNonAsync("comments", "posted", [TimeAgo.inWords(new Date(oComment.PostDate).getTime())]);
        if (oComment.ParentCommenter) {
            var replyingText = GetStringRawNonAsync("comments", "replyingTo", [oComment.ParentCommenter]);
        }

        // this is cursed
        var userText =  BBCodeParser.process(parser.parseFromString(oComment.PostText, "text/html").body.textContent).replaceAll(new RegExp(/(?<!=")(\b[\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/, 'g'), "<a href='$1' target='_blank'>$1</a>");

        //document.getElementById("user__badge").innerHTML = role['BadgeText'];
        //document.getElementById("user__badge").classList.add("badge-v2-" + role['Badge']);

        oBox.innerHTML = oBox.innerHTML +
            '<a href="/profiles?user=' + oComment.UserID + '"><img src="' + oComment.AvatarURL + '" class="comments__pb-user-pfp"></a>' +
            '<div class="comments__comment-div">' +
            '<div class="comments__comment-top">' +
            '<div class="comments__comment-top-username_area">' +
            '<a href="/profiles?user=' + oComment.UserID + '"><p class="comments__comment-top-username">' + oComment.Username + '</p></a>' +
            rolehtml +
            '</div>' +
            // this is cursed, so many steps of parsing and s
            '<p class="comments__comment-top-text">' + userText + '</p>' +
            '</div>' +
            '<div class="comments__comment-bottom">' +
            (oComment.UserID != nUserID || nRights > 1 ?
                `<div class="comments__comment-report" onclick="doReport('comment', ` + oComment.ID + `, {'commentText': '` + encodeURIComponent(oComment.PostText).replace(/'/g, "%27") + `'})">` +
                '<i aria-hidden="true" class="fas fa-exclamation-triangle"></i></div>'
                : '') +
            (oComment.UserID == nUserID || nRights > 0 ?
                '<div class="comments__comment-delete" onclick="deleteComment(' + oComment.ID + ');"><i aria-hidden="true" class="fas fa-trash"></i></div>'
                : '') +
            '<div class="comments__cb-inner">' +
            '<div class="comments__cbi-posted tooltip-v2" tooltip-content="' + new Date(oComment.PostDate).toUTCString() + '">' +
            '<p class="comments__cbi-posted-text">' + postedText + '</p>' +
            '</div>' +
            (oComment.ParentCommenter ? '<div class="comments__cbi-replying">' +
                '<p class="comments__cbi-replying-text"><i class="fas fa-reply"></i> ' + replyingText + '</p>' +
                '</div>' : '') +
            '</div>' +
            (bLoggedIn ?
                '<div onclick="openReply(\'' + oBox.id + '\');" class="comments__comment-reply"><i class="fas fa-reply"></i></div>'
                : '') +
            (bLoggedIn ?
                (oComment.HasVoted ?
                    '<div onclick="voteComment(' + oComment.ID + ');" class="comments__comment-vote comments__comment-vote-voted">' +
                    '<p id="Comment__' + oComment.ID + '" class="comments__comment-vote-text">+' + (oComment.VoteSum ?? 0) + '</p>' +
                    '</div>'
                    : '<div onclick="voteComment(' + oComment.ID + ');" class="comments__comment-vote">' +
                    '<p id="Comment__' + oComment.ID + '" class="comments__comment-vote-text">+' + (oComment.VoteSum ?? 0) + '</p>' +
                    '</div>')
                : '<div onclick="voteComment(' + oComment.ID + ');" class="comments__comment-vote">' +
                '<p id="Comment__' + oComment.ID + '" class="comments__comment-vote-text">+' + (oComment.VoteSum ?? 0) + '</p>' +
                '</div>') +
            '</div>' +
            '</div>';
        //oImgContainer.setAttribute("data-tippy-content", new Date(oAchievement.achieved_at).toDateString());
        //var date = new Date(oComment.PostDate).toUTCString();
        //oBox.querySelector(".comments__comment-bottom").setAttribute("data-tippy-content-comment-date", date);

        COMMENTS_boxes[MedalID].push(oBox);
    })
    if (Object.keys(COMMENTS_col_medals[MedalID]).length == Object.keys(COMMENTS_boxes[MedalID]).length) Comments_Out(oParent, MedalID);
}

function commentsSendClick(nVersionID = -1, nProfileId = -1) {
    Comments_CloseEmojiPopup();
    let strComment = document.getElementById("comments__input").value;

    if (strComment.includes("https://osu.ppy.sh/beatmapsets/")) {
        openDialog("Are you sure you want to post this comment?", "Your comment looks like it contains an osu! beatmap URL.", "If you're trying to post a beatmap, you should instead use the 'Add' button on the beatmaps panel.", "Post Anyway", function () {
            newComment(strComment, nVersionID, nProfileId);
            document.getElementById("comments__input").value = "";
        });
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

function voteComment(nID) {
    var xhr = createXHR(API_URL_COMMENTS);
    xhr.send("nObject=" + nID + "&nType=" + COMMENTS_type);

    xhr.onreadystatechange = function () {
        var oResponse = getResponse(xhr);
        if (handleUndefined(oResponse)) return;
        Object.keys(oResponse).forEach(function (obj) {
            if (oResponse[obj].HasVoted == 1) {
                document.getElementById("Comment__" + nID).innerHTML = "+" + (parseInt(document.getElementById("Comment__" + nID).innerHTML) - 1);
            } else {
                document.getElementById("Comment__" + nID).innerHTML = "+" + (parseInt(document.getElementById("Comment__" + nID).innerHTML) + 1);
            }
            document.getElementById("Comment__" + nID).parentElement.classList.toggle("comments__comment-vote-voted");
        });
    };
}

function openReply(strCommentId) {
    if (handleUndefined(document.getElementById(strCommentId).nextSibling) || !(document.getElementById(strCommentId).nextSibling.id == "oReplyBox")) {
        if (handleUndefined(document.getElementById("oReplyBox")) == false) document.getElementById("oReplyBox").remove();
        let oReferenceNode = document.getElementById(strCommentId);
        let oReplyBox = document.createElement("div");
        oReplyBox.id = "oReplyBox";
        oReplyBox.innerHTML = '<div class="comments__post-box comments__reply">' +
            '<img src="https://a.ppy.sh/' + nUserID + '" class="comments__pb-user-pfp">' +
            '<div class="comments__input-box">' +
            '<div class="comments__input-box-textarea">' +
            '<textarea onkeyup="textAreaAdjust(this)" style="overflow:hidden" id="reply__input" class="comments__input-box__text" rows="1"></textarea>' +
            '</div>' +
            '<button id="reply__send" class="comments__input-box__send">' +
            '<i class="fas fa-paper-plane"></i>' +
            '</button>' +
            '</div>' +
            '</div>';

        handleUndefined(document.getElementById(strCommentId).nextSibling) ? oReferenceNode.parentNode.insertBefore(oReplyBox, null) : oReferenceNode.parentNode.insertBefore(oReplyBox, oReferenceNode.nextSibling);

        document.getElementById("reply__send").addEventListener("click", () => {
            if (document.getElementById("reply__input").value.includes("https://osu.ppy.sh/beatmapsets/")) {
                openDialog("Are you sure you want to post this comment?", "Your reply looks like it contains an osu! beatmap URL.", "If you're trying to post a beatmap, you should instead use the 'Add' button on the beatmaps panel.", "Post Anyway", function () {
                    replySend();
                });
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

function textAreaAdjust(o) {
    //o.style.height = "1px";
    o.style.height = (o.scrollHeight) + "px";
}

function urlify(text) {
    var urlRegex = /(https?:\/\/[^\s]+)/g;
    return text.replace(urlRegex, function (url) {
        return '<a class="osekai__url" href="' + url + '">' + url + '</a>';
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
    let strHTML = "";
    COMMENTS_boxes[MedalID].forEach((oBox) => {
        strHTML += oBox.outerHTML;
    });
    oParent.innerHTML = strHTML;
}

function Comments_OpenEmojiPopup() {
    document.getElementById("comments__emoji_container").classList.toggle("comments_emoji_popup_conainer-hidden");
}

function Comments_CloseEmojiPopup() {
    if (document.getElementById("comments__emoji_container")) {
        document.getElementById("comments__emoji_container").classList.add("comments_emoji_popup_conainer-hidden");
    }
}