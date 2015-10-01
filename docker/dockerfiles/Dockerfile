FROM betavest/symfony-docker-centos-nginx-ph

ADD . /var/www
ADD docker/setup/container-files/etc/supervisor.d/permissions.conf /etc/supervisor.d/permissions.conf
ADD docker/setup/container-files/etc/nginx/hosts.d/prod.conf /etc/nginx/hosts.d/default.conf

## install phing
RUN pear channel-discover pear.phing.info
RUN pear install phing/phing