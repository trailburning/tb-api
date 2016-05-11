FROM debian:jessie

MAINTAINER Patrick Trost <mail@backendweb.de>

RUN echo "deb http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list
RUN echo "deb-src http://packages.dotdeb.org jessie all" >> /etc/apt/sources.list

ADD https://www.dotdeb.org/dotdeb.gpg .
RUN apt-key add dotdeb.gpg

RUN apt-get update && apt-get install -y php7.0-common php7.0-cli php7.0-fpm php7.0-mysql php7.0-gd php7.0-curl php7.0-intl php7.0-json php7.0-pgsql php7.0-opcache git

ADD symfony.ini /etc/php/7.0/fpm/conf.d/
ADD symfony.ini /etc/php/7.0/cli/conf.d/

ADD symfony.pool.conf /etc/php/7.0/fpm/pool.d/

RUN usermod -u 1000 www-data

RUN mkdir /run/php

CMD ["php-fpm7.0", "-F"]

EXPOSE 9000