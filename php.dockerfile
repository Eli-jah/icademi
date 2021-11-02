FROM php:7.4.25-fpm

# docker pull php:7.4.25-fpm

# docker container run -it --rm --name icademi_app \
# -p 9000:9000 -v "$PWD": /var/www/html -w  /var/www/html \
# php:7.4.25-fpm /bin/bash

# To install basic tools
RUN apt update && apt list --upgradable && apt upgrade -y && apt autoremove -y
RUN apt install -y gcc g++ make git wget vim zip net-tools build-essential

# To install crond service
RUN apt install -y cron

# To install mysql client

# RUN apt install -y mycli
# RUN ln -s /usr/bin/mycli /usr/bin/mysql

# To install nodejs & npm

# RUN curl -fsSL https://deb.nodesource.com/setup_lts.x | bash -
# RUN apt install -y nodejs
# RUN npm config set registry https://registry.npm.taobao.org

# To install yarn & @vue/cli

# RUN curl -sL https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /usr/share/keyrings/yarnkey.gpg >/dev/null
# RUN echo "deb [signed-by=/usr/share/keyrings/yarnkey.gpg] https://dl.yarnpkg.com/debian stable main" | tee /etc/apt/sources.list.d/yarn.list
# OR:
# RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
# RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# RUN apt update && apt install -y yarn
# RUN npm install -g @vue/cli

RUN apt update
RUN apt install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev
RUN apt install -y libxml2 libxml2-dev
RUN apt install -y libicu-dev
RUN apt install -y libmcrypt-dev libmcrypt4
RUN apt install -y libxslt1-dev
RUN docker-php-source extract
RUN docker-php-ext-install -j$(nproc) gd
# To install the MySQL extensions.
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-install bcmath gettext intl pcntl
RUN docker-php-ext-install shmop soap sockets sysvsem xmlrpc xsl
RUN pecl channel-update pecl.php.net
# To install the Redis extension.
RUN set -ex \
    && pecl update-channels \
    && pecl install redis-stable \
    && docker-php-ext-enable redis

# RUN apt update && apt install -y \
        # for docker-php-exe-install -j$(nproc) gd \
        # libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        # for docker-php-exe-install soap sockets sysvsem xmlrpc \
        # libxml2 libxml2-dev \
        # for docker-php-exe-install intl \
        # libicu-dev \
        # for docker-php-exe-install mcrypt \
        # libmcrypt-dev libmcrypt4 \
        # for docker-php-exe-install xsl \
        # libxslt1-dev \
    # && docker-php-source extract \
    # && cd /usr/src/php/ext/gd \
    # && docker-php-ext-configure gd \
    # && docker-php-ext-configure gd \
      # --with-webp-dir=/usr/include/webp \
      # --with-jpeg-dir=/usr/include \
      # --with-png-dir=/usr/include \
      # --with-freetype-dir=/usr/include/freetype2 \
    # && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql \
    # && docker-php-ext-install bcmath gettext intl mcrypt mysqli pcntl \
    # && docker-php-ext-install bcmath gettext intl mysqli pcntl \
    # && docker-php-ext-install shmop soap sockets sysvsem xmlrpc xsl zip \
    # && pecl channel-update pecl.php.net

#### Swoole ####

# RUN mkdir -p /usr/include/openssl

RUN pecl install -D 'enable-sockets="no" enable-openssl="no" enable-http2="yes" enable-mysqlnd="yes" enable-swoole-json="yes" enable-swoole-curl="no" enable-cares="yes"' swoole
# OR:
# RUN pecl install --configureoptions 'enable-sockets="no" enable-openssl="no" enable-http2="yes" enable-mysqlnd="yes" enable-swoole-json="yes" enable-swoole-curl="no" enable-cares="yes"' swoole

RUN echo "extension=swoole.so" > /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini

# RUN php -m | grep swoole
# RUN php --ri swoole

#### Swoole ####

#### Composer ####

# ENV COMPOSER_HOME /root/composer
# RUN curl -sS https://getcomposer.org/installer | php - -install-dir=/usr/local/bin -filename=composer
# RUN chmod a+x /usr/local/bin/composer

# RUN wget https://getcomposer.org/download/2.1.9/composer.phar -O /usr/local/bin/composer
# RUN chmod a+x /usr/local/bin/composer

RUN wget https://mirrors.aliyun.com/composer/composer.phar -O /usr/local/bin/composer
RUN chmod a+x /usr/local/bin/composer
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

#### Composer ####

COPY ./etc/profile/. /root/

# COPY . /var/www/html
WORKDIR /var/www/html
COPY . .

# RUN chmod +x /var/www/html/init.sh
# RUN ./init.sh

ENV TZ=Asia/Shanghai

EXPOSE 9000
EXPOSE 9501

# CMD [ "php", "./your-script.php" ]
CMD [ "php-fpm" ]
