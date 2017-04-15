FROM ubuntu:16.10

MAINTAINER Guillaume Maïssa <pro.g@maissa.fr>

# Install dependencies
RUN DEBIAN_FRONTEND=noninteractive apt-get update && \
    apt-get install -y apt-utils ca-certificates openssh-server sudo unzip strace tree vim htop \
        imagemagick gzip libxml2 curl git-core wget software-properties-common && \
    apt-get clean && \
    rm -rf /tmp/* /var/tmp/*

RUN DEBIAN_FRONTEND=noninteractive add-apt-repository ppa:ondrej/php && \
    apt-get update && \
    apt-get install --force-yes -y php7.1 php7.1-cli php7.1-curl php7.1-intl php7.1-common php7.1-mcrypt \
        php7.1-mysql php7.1-xsl php7.1-gd php7.1-memcached  php7.1-fpm && \
    apt-get clean && \
    rm -rf /tmp/* /var/tmp/*

RUN sed -i '/daemonize /c daemonize = no' /etc/php/7.1/fpm/php-fpm.conf && \
    sed -i '/^pid /c pid = /var/run/php-fpm.pid' /etc/php/7.1/fpm/php-fpm.conf && \
    sed -i '/^listen /c listen = 0.0.0.0:9000' /etc/php/7.1/fpm/pool.d/www.conf && \
    sed -i 's/^listen.allowed_clients/;listen.allowed_clients/' /etc/php/7.1/fpm/pool.d/www.conf

# Declaring netstat -lntpvolumes to be mounted
VOLUME /var/www/html/project
WORKDIR /var/www/html/project

#USER www-data

CMD ["php-fpm7.1", "-F"]
