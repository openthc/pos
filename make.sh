#!/bin/bash
#
# Top-Level Make the App Do Stuff Script
#

BIN_SELF=$(readlink -f "$0")
APP_ROOT=$(dirname "$BIN_SELF")

action="${1:-}"
shift

set -o errexit
set -o nounset

case "$action" in
# Build the CSS
css)

	echo "Build CSS"

	# // Something
	./node_modules/.bin/sass \
		--style compressed \
		--verbose \
		./sass:./webroot/css/

	ls -lh webroot/css/

	;;

minify)

	./bin/minify.php ./webroot/css/main.css ./webroot/js/main.js

	;;

# Update all the things
update)

	composer update --no-dev -a
	npm update

	./make.sh vendor
	./make.sh css

	;;

vendor)

	# lodash
	mkdir -p webroot/vendor/lodash/
	cp node_modules/lodash/lodash.min.js webroot/vendor/lodash/

	# jquery
	mkdir -p webroot/vendor/jquery/
	cp node_modules/jquery/dist/jquery.min.js webroot/vendor/jquery/
	cp node_modules/jquery/dist/jquery.min.map webroot/vendor/jquery/

	# jquery-ui
	mkdir -p webroot/vendor/jquery-ui/
	cp node_modules/jquery-ui/dist/jquery-ui.min.js webroot/vendor/jquery-ui/
	cp node_modules/jquery-ui/dist/themes/base/jquery-ui.min.css webroot/vendor/jquery-ui/

	# bootstrap
	mkdir -p webroot/vendor/bootstrap/
	cp node_modules/bootstrap/dist/css/bootstrap.min.css webroot/vendor/bootstrap/
	cp node_modules/bootstrap/dist/css/bootstrap.min.css.map webroot/vendor/bootstrap/
	cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js webroot/vendor/bootstrap/
	cp node_modules/bootstrap/dist/js/bootstrap.bundle.min.js.map webroot/vendor/bootstrap/

	# font awesome
	outpath="webroot/vendor/font-awesome"
	mkdir -p "$outpath"
	tar \
		-zx \
		--strip-components=1 \
		-f node_modules/@fortawesome/fontawesome-free/fortawesome-fontawesome-free-6.3.0.tgz \
		-C "$outpath"

	# qrcode
	mkdir -p webroot/vendor/qrcodejs
	cp node_modules/qrcodejs/qrcode.min.js webroot/vendor/qrcodejs/

	# chart.js
	outpath="webroot/vendor/chart.js"
	mkdir -p "$outpath"
	cp node_modules/chart.js/dist/chart.umd.js "$outpath/chart.min.js"
	cp node_modules/chart.js/dist/chart.umd.js.map "$outpath/"

	;;

# Help, the default target
*)

	echo
	echo "You must supply a make command"
	echo
	awk '/^# [A-Z].+/ { h=$0 }; /^[a-z]+.+\)/ { printf " \033[0;49;31m%-15s\033[0m%s\n", gensub(/\)$/, "", 1, $$1), h }' "$BIN_SELF" |sort
	echo

	;;

esac
