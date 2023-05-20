var list = document.getElementById("commentlist");

var testComment = {
    ID: 5724,
    PostText: "Just FC save me its just so easy smh",
    UserID: 26985250,
    PostDate: "2023-03-16 05:27:39",
    ParentCommenter: null,
    Parent: 0,
    Username: "Reefaroni11",
    AvatarURL: "https://a.ppy.sh/26985250?1665876844.jpeg",
    Pinned: null,
    MedalID: 44,
    Groups: null,
    HasVoted: null,
    UserID: 26985250,
    Username: "Reefaroni11",
    VoteSum: null,
}

list.appendChild(generateComment(testComment));
list.appendChild(generateComment(testComment));
list.appendChild(generateComment(testComment));
list.appendChild(generateComment(testComment));
list.appendChild(generateComment(testComment));
list.appendChild(generateComment(testComment));