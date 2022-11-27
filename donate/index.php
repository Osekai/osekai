<?php
$app = "home";
include_once($_SERVER['DOCUMENT_ROOT'] . "/global/php/functions.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/donate/php/functions.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="msapplication-TileColor" content="#553552">
    <meta name="theme-color" content="#553552">
    <meta name="description" content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <meta property="og:title" content="Osekai Donate" />
    <meta property="og:description" content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <meta name="twitter:title" content="Osekai Donate" />
    <meta name="twitter:description" content="we're just a small team, and by donating you help us keep the servers running and development going!" />
    <title name="title">Osekai Donate</title>
    <meta name="keywords" content="osekai,medals,osu,achievements,rankings,alternative,medal rankings,osekai,the,home,of,more">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="https://www.osekai.net/medals" />
    <style>
    body {
        --accentdark: 85, 53, 82 !important;
        --accent: 85, 53, 82 !important;
        --genericaccent: var(--accentdark);
    }
</style>
<?php echo $head; ?>

</head>

<?php
xhr_requests();
notification_system();
font();
css();
dropdown_system();
?>

<body>
    <div id="oPaymentPanel"></div>
    <?php navbar(); ?>

    <div class="osekai__panel-container">
        <div class="osekai__1col-panels">
            <div class="osekai__1col">
                <section class="osekai__panel osekai__header-panel">
                    <div class="osekai__panel-header">
                        <p><?php echo GetStringRaw("donate", "title") ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <p>
                        <?php echo GetStringRaw("donate", "body.p1") ?><br>
                            <br><?php echo GetStringRaw("donate", "body.p2") ?><br>
                            <br><?php echo GetStringRaw("donate", "body.p3") ?>
                        </p>
                    </div>
                </section>
            </div>
        </div>
        <div class="osekai__2col-panels">
            <div class="osekai__2col_col1">
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p><?php echo GetStringRaw("donate", "donate.title") ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <h2 class="osekai__h2"><?php echo GetStringRaw("donate", "donate.header") ?></h2>
                        <p><?php echo GetStringRaw("donate", "donate.body") ?></p>
                        <div class="osekai__divider"></div>
                        <div class="osekai__button-row">
                            <a class="osekai__button" id="btnPaypal"><?php echo GetStringRaw("donate", "donate.paypal") ?></a>
                            <a class="osekai__button" id="btnPaysafecard"><?php echo GetStringRaw("donate", "donate.paysafecard") ?></a>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p><?php echo GetStringRaw("donate", "costs.title") ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__costs-progress">
                            <div class="donate__progress-texts">
                                <h1 class="osekai__h1"><?php echo $percentage . "%"; ?></h1>
                                <p><?php echo GetStringRaw("donate", "costs.covered", ["€" . $donationTotal, "€" . $costs]) ?></p>
                            </div>
                            <div class="donate__progress-bar">
                                <div class="donate__progress-bar-inner" style="width: <?php echo $percentage . "%"; ?>;"></div>
                            </div>
                        </div>
                        <div class="donate__list">
                            <?php
                            foreach($payments as $k => $v) {
                            ?>
                            <div class="donate__panel">
                                <div class="donate__panel-header">
                                    <p class="donate__ph-text"><?php echo "€" . $payments[$k]['Amount']; ?></p>
                                    <p class="donate__ph-text donate__dh-text-right"><?php 
                                        $payDate = new DateTime($payments[$k]['PayDate']);
                                        echo $payDate->format('l\, jS F Y'); 
                                    ?></p>
                                </div>
                                <div class="donate__panel-body">
                                    <p class="donate__pb-text"><?php echo $payments[$k]['Reason']; ?></p>
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
                        <p><?php echo GetStringRaw("donate", "top.title") ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__list">
                            <?php
                            foreach($topdonators as $k => $v) {
                            ?>
                            <div class="donate__panel">
                                <div class="donate__panel-header">
                                    <p class="donate__ph-text"><?php echo $topdonators[$k]['Username']; ?></p>
                                </div>
                                <div class="donate__panel-body">
                                    <p class="donate__pb-money"><?php echo "€" . $topdonators[$k]['TotalDonation']; ?></p>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </section>
                <section class="osekai__panel">
                    <div class="osekai__panel-header">
                        <p><?php echo GetStringRaw("donate", "recent.title") ?></p>
                    </div>
                    <div class="osekai__panel-inner">
                        <div class="donate__list">
                            <?php
                            foreach($donations as $k => $v) {
                            ?>
                            <div class="donate__panel">
                                <div class="donate__panel-header">
                                    <p class="donate__ph-text"><?php echo $donations[$k]['Username']; ?></p>
                                    <p class="donate__ph-text donate__dh-text-right"><?php 
                                        $donoDate = new DateTime($donations[$k]['DonoDate']);
                                        echo $donoDate->format('l\, jS F Y'); 
                                    ?></p>
                                </div>
                                <div class="donate__panel-body">
                                    <p class="donate__pb-money"><?php echo "€" . $donations[$k]['DonoAmount']; ?></p>
                                    <div class="donate__right">
                                        <p class="donate__right-header"><?php echo GetStringRaw("donate", "recent.message") ?></p>
                                        <p class="donate__right-text"><?php if(!IsNullOrEmptyString($donations[$k]['Message'])) { 
                                                echo $donations[$k]['Message'];
                                            } else {
                                                echo "None";
                                            }; ?></p>
                                    </div>
                                </div>
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