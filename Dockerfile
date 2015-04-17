FROM ulff/symfony-docker-centos-nginx-ph

ADD . /var/www

## install phing
RUN pear channel-discover pear.phing.info
RUN pear install phing/phing

ENV SYMFONY__MONGO__HOST $MONGO_PORT_27017_TCP_ADDR
