#
# OpenTHC POS Docker Image
#

FROM openthc/web:latest

#
# Environment
ENV OPENTHC_SERVER_NAME="pos.docker.openthc.org"

#
# Install
# RUN git clone https://github.com/openthc/pos.git /opt/openthc/pos \
COPY ./ /opt/openthc/pos
RUN /opt/openthc/pos/make.sh

#
# Configure
COPY ./docker/apache2.conf  /etc/apache2/sites-enabled/000-default.conf
COPY ./docker/xdebug.ini    /etc/php/7.4/mods-available/xdebug.ini
COPY ./docker/init.sh       /init.sh

#
# Volumes
VOLUME [ "/opt/openthc/gfs" ]

#
# Ports
EXPOSE 80

#
# Go!
ENTRYPOINT [ "/init.sh" ]
