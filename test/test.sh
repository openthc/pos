#!/bin/bash
#
# OpenTHC Test Runner
#

set -o errexit
set -o nounset
# set -o pipefail

f=$(readlink -f "$0")
d=$(dirname "$f")
dt=$(date)

cd "$d"

report_path="../webroot/test-report"
if [ ! -d "$report_path" ]
then
	mkdir "$report_path"
fi

report_home="$report_path/index.html"

echo '<h1>Tests Started</h1>' > "$report_home"


#
#
../vendor/bin/phpunit \
	--log-junit "$report_path/output.xml" \
	--testdox-html "$report_path/testdox.html" \
	--testdox-text "$report_path/testdox.txt" \
	--testdox-xml "$report_path/testdox.xml" \
	--verbose \
	"$@" 2>&1 | tee "$report_path/output.txt"

# if [[ $ret != 0 ]]
# then
# 	echo "PHPUnit Failed"
# 	exit 1;
# fi
note=$(tail -n1 "$report_path/output.txt")

echo '<h1>Tests Completed</h1>' > "$report_home"

#
# Get Transform
echo '<h1>Transforming...</h1>' > "$report_home"
curl -qs https://openthc.com/pub/phpunit/report.xsl > report.xsl
xsltproc \
	--nomkdir \
	--output "$report_path/output.html" \
	report.xsl \
	"$report_path/output.xml"

#
# Final Ouptut
cat <<HTML > "$report_home"
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="theme-color" content="#247420">
<link rel="stylesheet" href="https://cdn.openthc.com/bootstrap/4.4.1/bootstrap.css" integrity="sha256-L/W5Wfqfa0sdBNIKN9cG6QA5F2qx4qICmU2VgLruv9Y=" crossorigin="anonymous">
<title>Test Result $dt</title>
</head>
<body>
<div class="container mt-4">
<div class="jumbotron">

<h1>Test Result $dt</h1>
<h2>$note</h2>

<p>You can view the <a href="output.txt">raw script output</a>,
or the <a href="output.xml">Unit Test XML</a>
which we've processed <small>(via XSL)</small> to <a href="output.html">a pretty report</a>
which is also in <a href="testdox.html">testdox format</a>.
</p>

</div>
</div>
</body>
</html>
HTML
