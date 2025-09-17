#
# OpenTHC POS Docker Image
#

FROM openthc/web:latest

#
# Install
RUN git clone https://github.com/openthc/pos.git /opt/openthc/pos \
	&& cd /opt/openthc/pos \
	&& /opt/openthc/pos/make.sh


#
# Copy
COPY ./docker/rootfs/apache2.conf     /etc/apache2/sites-enabled/pos.conf
COPY ./docker/rootfs/openthc-init.sh  /init.sh
# COPY ./rootfs/first-run.php    /openthc-config-init.php


#
# Volumes
VOLUME [ "/opt/openthc/gfs" ]


#
# Environment
ENV OPENTHC_SERVER_NAME="pos.docker.openthc.me"


ENTRYPOINT [ "/init.sh" ]
