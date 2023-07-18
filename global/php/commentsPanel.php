<section class="osekai__panel" id="comments-panel">
    <div class="osekai__panel-header osekai__panel-header-with-buttons">
        <div class="osekai__panel-hwb-left">
            <i class="fas fa-comments"></i>
            <p>
                <?= GetStringRaw("comments", "title"); ?>
            </p>
        </div>
        <div class="osekai__panel-hwb-right">
            <div class="osekai__panel-header-button osekai__dropdown-opener" id="filter__button">
                <p class="osekai__panel-header-dropdown-text osekai__dropdown-opener" id="filter__selected">
                    <?= GetStringRaw("comments", "sorting.votes"); ?>
                </p>
                <i class="fas fa-chevron-down osekai__panel-header-dropdown-icon"></i>
                <div class="osekai__dropdown osekai__dropdown-hidden" id="filter__list">
                    <div class="osekai__dropdown-item" id="filter__votes">
                        <?= GetStringRaw("comments", "sorting.votes"); ?>
                    </div>
                    <div class="osekai__dropdown-item" id="filter__date">
                        <?= GetStringRaw("comments", "sorting.newest"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="osekai__panel-inner">
        <div class="comments__area">
            <div id="comments__post_area" class="comments__post <?php if (!isset($_SESSION['osu'])) {
                echo 'comments__post-disabled';
            } else {
                echo '';
            } ?>">
                <div class="comments__post-left">
                    <img src="<?= getpfp(); ?>" class="comments__pfp">
                </div>
                <div class="comments__post-right">
                    <div class="comments__post-upper">
                        <textarea rows="1" placeholder="Post a comment!"  onKeyPress="Comments_CloseEmojiPopup()" id="comments__input"></textarea>
                    </div>
                    <div class="comments__post-lower">
                        <div onclick="Comments_OpenEmojiPopup()" id="comments__emoji"
                            class="comments__input-box__send comments__input-box__emoji">
                            <i class="fas fa-smile"></i>
                        </div>
                        <div class="comments_emoji_popup_conainer comments_emoji_popup_conainer-hidden"
                                id="comments__emoji_container">
                            </div>

                        <div class="comments__post-lower-right">
                            <p><?= GetStringRaw("comments", "post.bbcodeSupport"); ?></p>
                            <div class="comments__post-button" id="comments__send"><i class="fas fa-paper-plane"></i> <?= GetStringRaw("comments", "post"); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="osekai__divider"></div>
            <div>
                <div class="comments__main-comment-area" id="comments__box">
                </div>
            </div>
        </div>
    </div>
</section>