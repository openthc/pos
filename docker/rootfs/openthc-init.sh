#!/bin/bash
#
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail

# printenv | sort

# PHP Debugger
OPENTHC_DEBUG=${OPENTHC_DEBUG:-"false"}
if [ "$OPENTHC_DEBUG" == "true" ]
then
	echo "DEBUG ENABLED"
	phpenmod xdebug
fi

# if [ -f /first-run.php ]
# then
# 	echo "RUN0"
# 	php /first-run.php
# 	rm /first-run.php
# else
# 	echo "RUN1+"
# fi


# The Service Init Script
# php /opt/openthc/pos/init.php


# Start Regular Way
exec /usr/sbin/apache2 -DFOREGROUND
