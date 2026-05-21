FROM php:8.5-fpm

RUN apt-get update && apt-get install -y curl git mc

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash -E
RUN apt-get install -y symfony-cli

WORKDIR /var/www/test3
RUN git config --global user.email "after@ya.ru"
RUN git config --global user.name "Maxim Gabidullin"

RUN apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl

RUN apt-get install -y libxml2-dev
RUN docker-php-ext-install xml

RUN apt-get install -y libonig-dev
RUN docker-php-ext-install mbstring

RUN apt-get install -y libicu-dev
RUN docker-php-ext-install intl

RUN docker-php-ext-install pdo pdo_mysql

CMD ["php-fpm"]
