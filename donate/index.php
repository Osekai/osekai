<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/donate/php/functions.php");
$accent_override = [[85, 53, 82], [85, 53, 82]];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#553552">
    <meta name="theme-color" content="#553552">
    <meta name="description"
        content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <meta property="og:title" content="Osekai Donate" />
    <meta property="og:description"
        content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <meta name="twitter:title" content="Osekai Donate" />
    <meta name="twitter:description"
        content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <title name="title">Osekai Donate</title>
    <meta name="keywords"
        content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="<?= ROOT_URL ?>/medals" />
    <?= $head; ?>

</head>

<?php
xhr_requests();
notification_system();
font();
css();
?>

<body>
    <div id="oPaymentPanel"></div>
    <?php navbar(); ?>
    <div class="donate__header">
        <h1>
            <?= GetStringRaw("donate", "title") ?>
        </h1>
        <p>
            <?= GetStringRaw("donate", "body.p1") ?>
        </p>
        <p>
            <?= GetStringRaw("donate", "body.p2") ?>
        </p>
        <p>
            <?= GetStringRaw("donate", "body.p3") ?>
        </p>
    </div>
    <div class="osekai__panel-container">
        <div class="osekai__2col-panels">
            <div class="osekai__2col_col1">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>
                            <?= GetStringRaw("donate", "donate.title") ?>
                        </p>
                    </div>
                    <div class="osekai__panel-inner">
                        <h2 class="osekai__h2">
                            <?= GetStringRaw("donate", "donate.header") ?>
                        </h2>
                        <p>
                            <?= GetStringRaw("donate", "donate.body") ?>
                        </p>
                        <div class="osekai__button-row">
                            <a class="osekai__button" id="btnPaypal">
                                <?= GetStringRaw("donate", "donate.paypal") ?>
                            </a>
                            <a class="osekai__button" id="btnPaysafecard">
                                <?= GetStringRaw("donate", "donate.paysafecard") ?>
                            </a>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>
                            <?= GetStringRaw("donate", "costs.title") ?>
                        </p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__costs-progress">
                            <div class="donate__progress-texts">
                                <h1 class="osekai__h1">
                                    <?= $percentage . "%"; ?>
                                </h1>
                                <p>
                                    <?= GetStringRaw("donate", "costs.covered", ["€" . $donationTotal, "€" . $costs]) ?>
                                </p>
                            </div>
                            <div class="donate__progress-bar">
                                <div class="donate__progress-bar-inner" <?php if ($percentage < 100) {
                                    echo "style=\"width: {$percentage}%;\"";
                                } ?>></div>
                            </div>
                        </div>
                        <div class="donate__list">
                            <?php
                            foreach ($payments as $k => $v) {
                                ?>
                                <div class="donate__recent-panel">
                                    <div class="donate__recent-panel-inner" style="padding-top: 18px; padding-bottom: 18px;">
                                        <div class="donate__panel-texts">
                                            <h1>
                                                <?= "€" . $payments[$k]['Amount']; ?>
                                            </h1>
                                        </div>
                                        <div class="donate__panel-texts-right">
                                            <?php
                                            $payDate = new DateTime($payments[$k]['PayDate']);
                                            echo $payDate->format('l\, jS F Y');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="donate__recent-panel-message">
                                        <?= $payments[$k]['Reason']; ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </section>
            </div>
            <div class="osekai__2col_col2">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>
                            <?= GetStringRaw("donate", "top.title") ?>
                        </p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__list">
                            <?php
                            foreach ($topdonators as $k => $v) {
                                ?>
                                <div class="donate__panel">
                                    <div class="donate__panel-inner">
                                        <img src="https://a.ppy.sh/<?= $topdonators[$k]['osuId']; ?>">
                                        <div class="donate__panel-texts">
                                            <h1>
                                                <?= $topdonators[$k]['Username']; ?>
                                            </h1>
                                            <p>
                                                €
                                                <?= $topdonators[$k]['TotalDonation']; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p>
                            <?= GetStringRaw("donate", "recent.title") ?>
                        </p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__list">
                            <?php
                            foreach ($donations as $k => $v) {
                                ?>
                                <!-- echo $donations[$k]['Message']; -->

                                <div class="donate__recent-panel">
                                    <div class="donate__recent-panel-inner">
                                        <img src="https://a.ppy.sh/<?= $donations[$k]['osuID']; ?>">
                                        <div class="donate__panel-texts">
                                            <h1>
                                                <?= $donations[$k]['Username']; ?>
                                            </h1>
                                            <p>
                                                <?= "€" . $donations[$k]['DonoAmount']; ?>
                                            </p>
                                        </div>
                                        <div class="donate__panel-texts-right">
                                            <?php
                                            $donoDate = new DateTime($donations[$k]['DonoDate']);
                                            echo $donoDate->format('l\, jS F Y');
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                    if (!IsNullOrEmptyString($donations[$k]['Message'])) {
                                        ?>
                                        <div class="donate__recent-panel-message">
                                            <?= $donations[$k]['Message'] ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var bLoggedIn = (typeof nUserID !== 'undefined' && nUserID.toString() !== "-1");

        document.getElementById("btnPaypal").addEventListener("click", () => {
            document.getElementById("oPaymentPanel").innerHTML = '<div class="osekai__overlay"> ' +
                '<section class="osekai__panel osekai__overlay__panel"> ' +
                '<div class="osekai__panel-header"> ' +
                '<p>Donate</p> ' +
                '</div> ' +
                '<form action="./api/api.php" method="POST">' +
                '<div class="osekai__panel-inner osekai__flex-vertical-container"> ' +
                '<p>Thanks for supporting us! :)</p>' +
                '<input type="textarea" name="Message" id="Message" class="osekai__input" rows="4" cols="50" placeholder="Message (optional)"></input>' +
                (bLoggedIn ?
                    '<div class="osekai__flex_row osekai__fr_centered">' +
                    '<input class="osekai__checkbox" id="Anonymous" name="Anonymous" type="checkbox">' +
                    '<label for="Anonymous"></label>' +
                    '<p class="osekai__checkbox-label">Anonymous</p>' +
                    '</div>' : '') +
                '<div class="osekai__flex_row"> ' +
                '<a class="osekai__button" onclick="closePaymentPanel();">Cancel</a> ' +
                '<input type="submit" class="osekai__button osekai__left" value="Donate"></input> ' +
                '</div> ' +
                '</div> ' +
                '</form>' +
                '</section> ' +
                '</div>';
        });

        document.getElementById("btnPaysafecard").addEventListener("click", () => {
            document.getElementById("oPaymentPanel").innerHTML = '<div class="osekai__overlay"> ' +
                '<section class="osekai__panel osekai__overlay__panel"> ' +
                '<div class="osekai__panel-header"> ' +
                '<p>Donate</p> ' +
                '</div> ' +
                '<form action="./api/api.php" method="POST">' +
                '<div class="osekai__panel-inner osekai__flex-vertical-container"> ' +
                '<p>Thanks for supporting us! :)</p>' +
                '<input type="text" name="Code" class="osekai__input" placeholder="Code" pattern="^[0-9]{16}$|^[0-9]{4}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}$|^[0-9]{4}\-[0-9]{4}\-[0-9]{4}\-[0-9]{4}$" required></input>' +
                '<input type="textarea" name="Message" class="osekai__input" rows="4" cols="50" placeholder="Message (optional)"></input>' +
                (bLoggedIn ?
                    '<div class="osekai__flex_row osekai__fr_centered">' +
                    '<input class="osekai__checkbox" id="Anonymous" name="Anonymous" type="checkbox">' +
                    '<label for="Anonymous"></label>' +
                    '<p class="osekai__checkbox-label">Anonymous</p>' +
                    '</div>' : '') +
                '<div class="osekai__flex_row"> ' +
                '<a class="osekai__button" onclick="closePaymentPanel();">Cancel</a> ' +
                '<input type="submit" class="osekai__button osekai__left" value="Donate"></input> ' +
                '</div> ' +
                '</div> ' +
                '</form>' +
                '</section> ' +
                '</div>';
        });

        function closePaymentPanel() {
            document.getElementById("oPaymentPanel").innerHTML = "";
        }
    </script>
</body>
<?php include($_SERVER['DOCUMENT_ROOT'] . "/global/php/functionsEnd.php"); ?>