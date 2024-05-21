FROM php:7.4-fpm

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && \
    install-php-extensions amqp apcu bcmath exif gd grpc imap intl ldap mcrypt opcache pgsql pdo_pgsql redis sockets uuid xdebug yaml zip   





FROM php:7.4-fpm
RUN apt-get update && apt-get install -y \
		libfreetype-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install mysqli pdo pdo_mysql bcmath\
  && pecl install redis-5.3.7 \
  && docker-php-ext-enable redis \
  && pecl install xdebug-2.9.0 \
  && docker-php-ext-enable xdebug \
  && rm -rf /var/lib/apt/lists/*
