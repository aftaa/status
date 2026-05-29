FROM php:8.5-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    git \
    mc \
    libicu-dev \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony \

RUN printf "no\n" | pecl install redis && docker-php-ext-enable redis

ARG PUID=1000
ARG PGID=1000
RUN groupadd -g ${PGID} appgroup && useradd -u ${PUID} -g appgroup -m appuser
USER appuser

RUN git config --global user.email "after@ya.ru"
RUN git config --global user.name "Maxim Gabidullin"

WORKDIR /var/www/test3

CMD ["php-fpm"]
