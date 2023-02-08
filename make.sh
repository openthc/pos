#!/bin/bash
#
# Top-Level Make the App Do Stuff Script
#

BIN_SELF=$(readlink -f "$0")
APP_ROOT=$(dirname "$BIN_SELF")

action="${1:=help}"
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

# Help, the default target
*)

	echo
	echo "You must supply a make command"
	echo
	awk '/^# [A-Z].+/ { h=$0 }; /^[a-z]+.+\)/ { printf " \033[0;49;31m%-15s\033[0m%s\n", gensub(/\)$/, "", 1, $$1), h }' "$BIN_SELF" |sort
	echo

	;;

esac
