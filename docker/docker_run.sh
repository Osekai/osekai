#!/bin/bash

echo "Generating config.php for environment variables..."

ensure_not_null() {
    if [ -z "${!1}" ]; then 
        echo "$1 environment variable is null or empty. Please, set its value before this script is runs.";
        exit -1;
    fi
}

ensure_not_null "ROOT_URL"
ensure_not_null "OSU_API_V1_KEY"
ensure_not_null "OSU_OAUTH_CLIENT_SECRET"
ensure_not_null "OSU_OAUTH_CLIENT_ID"

ensure_not_null "DB_USER"
ensure_not_null "DB_PASSWORD"
ensure_not_null "DB_NAME"
ensure_not_null "DB_HOSTNAME"

cat >/var/www/html/config.php <<EOL
<?php
define("MODE", "local");
define("ENVIRONMENT", "apache2");
define("AUTHENTICATOR_KEY", "key");
define("VPS_IP", "ignore"); 

define("EXPERIMENTAL_KEY", "on");

define("OSU_API_V1_KEY", "$OSU_API_V1_KEY");
define("OSU_OAUTH_CLIENT_SECRET", "$OSU_OAUTH_CLIENT_SECRET");
define("OSU_OAUTH_CLIENT_ID", "$OSU_OAUTH_CLIENT_ID");
define("OSU_OAUTH_REDIRECT_URI", "http://${ROOT_URL}/global/php/login.php");

define("CHANGELOG_KEY", "$CHANGELOG_KEY");

define("DB_USER", "$DB_USER");
define("DB_PASSWORD", "$DB_PASSWORD");
define("DB_NAME", "$DB_NAME");
define("DB_HOSTNAME", "$DB_HOSTNAME");

define("SNAPSHOTS_WEBHOOK", "$SNAPSHOT_WEBHOOK_URL");
define("SCRIPTS_RUST_KEY", "$SCRIPTS_RUST_KEY");

define("LANG_UPDATE_KEY", "$LANG_UPDATE_KEY");
define("CROWDIN_API_KEY", "$CROWDIN_API_KEY");

define("ROOT_URL", "http://${ROOT_URL}"); 
EOL

apache2-foreground