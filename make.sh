#!/bin/bash
#
# Install Helper
#
# SPDX-License-Identifier: GPL-3.0-only
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail

APP_ROOT=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

cd "$APP_ROOT"

composer install --no-ansi --no-progress --classmap-authoritative

npm install --ignore-scripts --no-audit --no-fund

vendor/openthc/common/lib/make.sh install_bootstrap
vendor/openthc/common/lib/make.sh install_fontawesome
vendor/openthc/common/lib/make.sh install_jquery
vendor/openthc/common/lib/make.sh install_lodash
vendor/openthc/common/lib/make.sh install_htmx
# bash -x /opt/openthc/common/lib/make.sh install_echarts

# qrcode
mkdir -p webroot/vendor/qrcodejs
cp node_modules/qrcodejs/qrcode.min.js webroot/vendor/qrcodejs/

# chart.js
mkdir -p webroot/vendor/chart.js
cp node_modules/chart.js/dist/chart.umd.js webroot/vendor/chart.js/
cp node_modules/chart.js/dist/chart.umd.js.map webroot/vendor/chart.js/

# SASS
./node_modules/.bin/sass \
	--fatal-deprecation 1.80.0 \
	--no-charset \
	--style=compressed \
	--stop-on-error \
	sass/main.scss webroot/css/main.css
