<?php
// modes:
// - production
//   live site on osekai.net, same setup
// - local
//   same as live site, but with admin and expeirmental walls removed
// - dev
//   everyone has experimental mode, even when logged out
define("MODE", "local");
define("OSEKAI_VERSION", "2.0.2.0.9"); // cache invalidation
define("AUTHENTICATOR_KEY", "j97f3j02vn8eqwhv0sejavny8va-9k0awnf7");
define("VPS_IP", "ignore"); // internal stuff, such as reporting and alerts. ignore

define("EXPERIMENTAL_KEY", "EGAETwbjCW4CfnTZpPVvEpnd59NpuAxm");

define("OSU_API_V1_KEY", "7a149044e9dc45b7c7250b4afe260e5742d3b4ba");
define("OSU_OAUTH_CLIENT_SECRET", "RZtJrIuVbXTMHeVUWS2XbShet663TfJB73kDhWzb");
define("OSU_OAUTH_CLIENT_ID", 19035);
define("OSU_OAUTH_REDIRECT_URI", "http://localhost:8080/global/php/login.php");

define("DB_USER", "tanza");
define("DB_PASSWORD", "password");
define("DB_NAME", "osekai");
define("DB_HOSTNAME", "localhost");

define("SNAPSHOTS_WEBHOOK", "webhook_url");
define("SCRIPTS_RUST_KEY", "lmfUQwfyMuxYvtpkm7aPb78vbp8SG7GjjOmCYwNJ3MRVqagHNH");