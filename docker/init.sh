#!/bin/bash
#
# OpenTHC POS Docker Init
#

set -o errexit
set -o errtrace
set -o nounset
set -o pipefail


#
# PHP Debugger
OPENTHC_DEBUG=${OPENTHC_DEBUG:-"false"}
if [ "$OPENTHC_DEBUG" == "true" ]
then
	echo "DEBUG ENABLED"
	phpenmod xdebug
fi


#
# Uses Environment to Create App
/opt/openthc/pos/docker/init.php


#
# Unsets All OpenTHC Environment Variables
# Except for OPENTHC_SERVICE and OPENTHC_SERVER_NAME
for var in $(env | cut -d= -f1 | grep OPENTHC | grep -v OPENTHC_SER)
do
	# echo "unset $var"
	unset "$var"
done


#
# Start Apache
exec /usr/sbin/apache2 -DFOREGROUND
