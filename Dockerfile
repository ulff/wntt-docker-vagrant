FROM ulff/symfony-docker-centos-nginx-ph

ADD . /var/www

## install phing
RUN pear channel-discover pear.phing.info
RUN pear install phing/phing