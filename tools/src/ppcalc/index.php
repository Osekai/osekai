<div class="tools__tool-generic-background"></div>
<div class="tools__tool">
    <section class="osekai__panel">
        <div class="osekai__panel-header">
            <p>Taiko PP Calculator</p>
        </div>
        <div class="osekai__panel-inner">
            <div class="osekai__generic-warning">
                <i class="fas fa-info-circle" aria-hidden="true"></i>
                <p>This is all super WIP, ui is very so not final.</p>
            </div>
            <br><br>
            <p>Star Rating</p>
            <input id="strain" value="5" class="osekai__input" type="number" min="0" step="0.1"></input>
            <br>
            <p>Max Possible Combo:</p><input value="500" id="numcircles" class="osekai__input" type="number" />
            <br>
            <p>Overall Difficulty:</p><input value="5" id="od-input" class="osekai__input" type="number" step="0.1" oninput="displayOD();" />
            <br>
            <i>
                <div id="od-scaled"></div>
                <div id="od-label"></div>
            </i>
            <br>
            <p>Misses:</p><input value="0" id="misses" class="osekai__input" type="number" oninput="setActive('misses');">
            <br>
            <p>Accuracy %:</p><input value="99" id="acc" class="osekai__input" type="number" step="0.1" oninput="setActive('acc');">
            <br>
            <p>100s hit:</p><input value="0" id="100s" class="osekai__input" type="number" oninput="setActive('100s');">

            <br>

            <div id="mods">
                <input id="EZ" type="checkbox"><label for="EZ"></label>
                <input id="HR" type="checkbox"><label for="HR"></label>
                <input id="HT" type="checkbox"><label for="HT"></label>
                <input id="DT" type="checkbox"><label for="DT"></label>
                <input id="HD" type="checkbox"><label for="HD"></label>
                <input id="FL" type="checkbox"><label for="FL"></label>
            </div>

            <br>

            <h1 id="pp"></h1>
        </div>
    </section>
    <br>
    <button class="selected osekai__button">taiko</button>
</div>