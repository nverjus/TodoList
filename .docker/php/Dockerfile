FROM php:7.2-fpm

##<romaricp>##
RUN apt-get update \
    && apt-get install -y --no-install-recommends vim curl debconf subversion git apt-transport-https apt-utils \
    build-essential locales acl mailutils wget nodejs zip unzip \
    gnupg gnupg1 gnupg2

RUN docker-php-ext-install pdo pdo_mysql

COPY php.ini /etc/php/7.2.3/php.ini
COPY php-fpm-pool.conf /etc/php/7.2.3/pool.d/www.conf

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
	mv composer.phar /usr/local/bin/composer

RUN	echo "deb https://deb.nodesource.com/node_8.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
	wget -nv -O -  https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - && \
	echo "deb-src https://deb.nodesource.com/node_8.x jessie main" >> /etc/apt/sources.list.d/nodejs.list && \
    apt-get update && \
	apt-get install -y --force-yes nodejs && \
	rm -f /etc/apt/sources.list.d/nodejs.list

  RUN version=$(php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;") \
      && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/$version \
      && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
      && mv /tmp/blackfire-*.so $(php -r "echo ini_get('extension_dir');")/blackfire.so \
      && printf "extension=blackfire.so\nblackfire.agent_socket=tcp://blackfire:8707\n" > $PHP_INI_DIR/conf.d/blackfire.ini

RUN groupadd dev -g 999
RUN useradd dev -g dev -d /home/dev -m

RUN rm -rf /var/lib/apt/lists/*
RUN echo "en_US.UTF-8 UTF-8" > /etc/locale.gen && \
    echo "fr_FR.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen

WORKDIR /home/wwwroot/
##</romaricp>##

EXPOSE 9000
CMD ["php-fpm"]
