#!/bin/bash
#
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail

printenv | sort

if [ -f /first-run.php ]
then
	echo "RUN0"
	php /first-run.php
	rm /first-run.php
else
	echo "RUN1+"
fi

# Start Regular Way
exec /usr/sbin/apache2 -DFOREGROUND
