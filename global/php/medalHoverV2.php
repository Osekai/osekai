<div class="osekai__medal-popup osekai__medal-popup-closed" id="mhv2-overlay">
    <div class="osekai__medal-popup-inner">
        <div class="osekai__medal-popup-top">
            <div class="osekai__medal-popup-top-left">
                <img id="mhv2-icon-blur" src="/medals/img/unknown_medal.png" class="osekai__medal-popup-top-left-icon osekai__medal-popup-top-left-icon-blur">
                <img id="mhv2-icon" src="/medals/img/unknown_medal.png" class="osekai__medal-popup-top-left-icon">
            </div>
            <div class="osekai__medal-popup-top-right">
                <div class="osekai__medal-popup-top-right-texts">
                    <h3 id="mhv2-name">Medal Name</h3>
                    <p id="mhv2-text">Medal Text</p>
                    <div id="mhv2-mods" class="osekai__medal-popup-mods">

                    </div>
                </div>
            </div>
        </div>
        <div class="osekai__medal-popup-bottom">
            <div class="osekai__medal-popup-bottom-inner">
                <h3>Solution</h3>
                <p id="mhv2-solution">Cool solution text</p>
            </div>
            <div class="osekai__medal-popup-bottom-toolbar">
                <div class="osekai__medal-popup-bottom-toolbar-left">
                    <div class="osekai__button" onclick="medalPopupV2.hideOverlay()">Close</div>
                </div>
                <div class="osekai__medal-popup-bottom-toolbar-right">
                    <a class="osekai__button" target="_blank" id="mhv2-open-button">Open in new tab</a>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/global/css/components/medalPopupV2.css?v=<?php echo OSEKAI_VERSION; ?>">
<script src="/global/js/components/medalPopupV2.js?v=<?php echo OSEKAI_VERSION; ?>"></script>