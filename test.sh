#!/bin/bash -x
#
# OpenTHC Test Runner
#

set -o errexit
set -o nounset

x=${OPENTHC_TEST_BASE:-}
if [ -z "$x" ]
then
	echo "You have to define the environment first"
	exit 1
fi

f=$(readlink -f "$0")
d=$(dirname "$f")

cd "$d"

declare -rx OUTPUT_BASE="webroot/test-output"
declare -rx OUTPUT_MAIN="${OUTPUT_BASE}/index.html"

mkdir -p "${OUTPUT_BASE}"

SOURCE_LIST=(
	boot.php
	bin/
	lib/
	sbin/
	test/
	view/
)


#
# Lint
vendor/openthc/common/test/phplint.sh


#
# PHP-CPD
vendor/openthc/common/test/phpcpd.sh


#
# PHPStan
vendor/openthc/common/test/phpstan.sh


#
# PHPUnit
vendor/openthc/common/test/phpunit.sh "$@"


#
# Final Output
test_date=$(date)
test_note=$(tail -n1 "${OUTPUT_BASE}/phpunit.txt")

cat <<HTML > "${OUTPUT_MAIN}"
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="theme-color" content="#069420">
<style>
html {
	font-family: sans-serif;
	font-size: 1.5rem;
}
</style>
<title>Test Result ${test_date}</title>
</head>
<body>
<h1>Test Result ${test_date}</h1>
<h2>${test_note}</h2>
<p>Linting: <a href="phplint.txt">phplint.txt</a></p>
<p>PHPCPD: <a href="phpcpd.txt">phpcpd.txt</a></p>
<p>PHPStan: <a href="phpstan.xml">phpstan.xml</a> and <a href="phpstan.html">phpstan.html</a></p>
<p>PHPUnit: <a href="phpunit.txt">phpunit.txt</a>, <a href="phpunit.xml">phpunit.xml</a> and <a href="phpunit.html">phpunit.html</a></p>
<p>Textdox: <a href="testdox.txt">testdox.txt</a>, <a href="testdox.xml">testdox.xml</a> and <a href="testdox.html">testdox.html</a></p>
</body>
</html>
HTML
