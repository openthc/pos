#!/bin/bash
#
# Does make like things
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail

APP_ROOT=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

cd "$APP_ROOT"

composer install --no-ansi --no-progress --classmap-authoritative

npm install --no-audit --no-fund --package-lock-only

php <<PHP
<?php
require_once(__DIR__ . '/boot.php');
\OpenTHC\Make::install_bootstrap();
\OpenTHC\Make::install_fontawesome();
\OpenTHC\Make::install_jquery();
PHP

# lodash
mkdir -p webroot/vendor/lodash/
cp node_modules/lodash/lodash.min.js webroot/vendor/lodash/

# htmx
# mkdir -p webroot/vendor/htmx
# cp node_modules/htmx.org/dist/htmx.min.js webroot/vendor/htmx/

# qrcode
mkdir -p webroot/vendor/qrcodejs
cp node_modules/qrcodejs/qrcode.min.js webroot/vendor/qrcodejs/

# chart.js
mkdir -p webroot/vendor/chart.js
cp node_modules/chart.js/dist/chart.umd.js webroot/vendor/chart.js/
cp node_modules/chart.js/dist/chart.umd.js.map webroot/vendor/chart.js/

#
# SASS
./node_modules/.bin/sass \
	--fatal-deprecation 1.65.0 \
	--no-charset \
	--no-source-map \
	--style=compressed \
	--stop-on-error \
	sass/main.scss webroot/css/main.css \
	2>&1;

#
#
php <<PHP
<?php
require_once(__DIR__ . '/boot.php');
\OpenTHC\Make::create_homepage('pos');
PHP
