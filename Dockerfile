FROM ulff/symfony-docker-centos-nginx-ph

ADD . /var/www

## install phing
RUN pear channel-discover pear.phing.info
RUN pear install phing/phing

ENV SYMFONY__MONGO__HOST mongo
ENV SYMFONY__MONGO__PORT 27017