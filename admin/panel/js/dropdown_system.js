window.addEventListener('click', function (e) {
    if (!e.target.classList.contains("basic-page-dropdown") && !e.target.classList.contains("dropdown-item") && !e.target.classList.contains("basic-page-dropdown-opener") && (e.target.closest(".basic-page-dropdown-opener") == null)) {
        document.querySelectorAll(".basic-page-dropdown").forEach((colItems) => {
            colItems.classList.add("basic-page-dropdown-hidden");
        });
    }
});