function createUserTippys() {
    tippy(`user`, {
        interactive: true,
        offset: [0, 0],
        placement: 'bottom-start',
        appendTo: () => document.body,
        zIndex: 99,
        delay: [400, null],
        //trigger: 'click',
        interactiveBorder: 30,
        onCreate(instance) {
            instance._isFetching = false;
            instance._data = null;
            instance._error = null;
            instance._popper = {};
            instance._box = {};
        },        
        render(instance) {
            instance._popper = document.createElement(`div`);
            instance._box = document.createElement(`div`);
            instance._popper.appendChild(instance._box);
            instance._box.className = `user__tooltip-box`;
            instance._box.innerHTML = `<div class="user__tooltip-body">
                        <div class="user__tooltip-header">
                            <img class="user__tooltip-avatar" src="https://a.ppy.sh/${instance.reference.getAttribute('id')}" alt="Avatar">
                            <div class="user__tooltip-info">
                                <div class="user__tooltip-user">
                                    <div class="user__tooltip-username">${instance.reference.innerHTML}</div>
                                    <div class="user__tooltip-userid">${instance.reference.getAttribute('id')}</div>
                                </div>
                                <span class="user__tooltip-stats">Loading...</span>
                            </div>
                        </div>
                        <div class="user__tooltip-footer">
                            <div>
                                <a class="button" href="https://osu.ppy.sh/u/${instance.reference.getAttribute('id')}">osu! profile</a>
                                <a class="button" href="/profiles/?mode=all&user=${instance.reference.getAttribute('id')}">osekai profile</a>
                            </div>
                            <a class="button button-danger" onclick="restrictUser(${instance.reference.getAttribute('id')})">Restrict</a>
                        </div>`;
            const popper = instance._popper;
            return {
                popper
            };
        },
        onShow(instance) {
            if (instance._isFetching || instance._data || instance._error) {
                return;
            }

            instance._isFetching = true;
            fetch(`/admin/panel/api/base/users/user?nUserID=${instance.reference.id}`)
                .then((response) => response.json())
                .then((data) => {
                    instance.popper.querySelector(".user__tooltip-stats").innerHTML = `<strong>${data.comments}</strong> comments <strong>${data.beatmaps}</strong> beatmaps <strong>${data.versions}</strong> versions (azelia)`;
                    // if(data.restricted)
                    // box.querySelector(".button.button-danger").onclick = () =>
                })
                .catch((error) => {
                    // Fallback if the network request failed
                    instance.popper.querySelector(".user__tooltip-stats").innerHTML = `Request failed. ${error}`; 
                }).finally(() => {
                    instance._isFetching = false;
                });
        },

    });
}

function restrictUser(id) {
    new modalPopup(
        `Restrict user`,
        `<div class="form-input">
            <p>User ID</p>
            <input type="text" class="input input-pattern" value="${id}" placeholder="User ID" disabled pattern="[0-9]{0,8}">
        </div>
        <form class="form-input" id="modal__report-reason-form">
            <p>Reason</p>
            <textarea class="input" name="reason" id="modal__report-reason" placeholder="None" form="modal__report-reason-form"></textarea>
        </form>`,
        `<a class="button button-danger" id="modal__restrict-user">Restrict</a>`,
        (e, modal) => {
            if (!e && !modal) return;
            if (e.target.id == "modal__restrict-user") {
                alert(`User: ${id} has been restricted for ${modal.querySelector("#modal__report-reason-form").reason.value}.`);
                xhr = createXHR("/admin/panel/api/home/restrictions/restrict");
                xhr.send(`nUserID=${id}&strReason=${encodeURIComponent(modal.querySelector("#modal__report-reason-form").reason.value)}`);
                xhr.onerror = function () {
                    new modalPopup("Error", `The user: ${id} is currently restricted.<br>Please verify you have the correct user.`, `<a class="button">OK</a>`);
                }
                xhr.onload = function () {
                    new modalPopup("Success", `The user: ${id} has been restricted.<br>Reason: ${modal.querySelector("#modal__report-reason-form").reason.value}`, `<a class="button">OK</a>`);
                }
            }
        });
}