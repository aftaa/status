# ==========================================
# STAGE 1: base
# ==========================================
FROM php:8.5-fpm AS base

RUN apt-get update

RUN apt-get install -y librabbitmq-dev
RUN pecl install amqp && docker-php-ext-enable amqp

RUN apt-get install -y --no-install-recommends \
    curl \
    git \
    mc \
    unzip \
    supervisor \
    libcurl4-openssl-dev \
    libxml2-dev \
    libonig-dev \
    libicu-dev \
    libssl-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) intl pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN printf "no\n" | pecl install redis && docker-php-ext-enable redis
RUN pecl install mongodb && docker-php-ext-enable mongodb

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN mkdir -p /var/log/supervisor /etc/supervisor/conf.d
COPY docker/php/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/php/supervisor/messenger.conf /etc/supervisor/conf.d/messenger.conf

WORKDIR /var/www/test3

# ==========================================
# STAGE 2: dev
# ==========================================
FROM base AS dev

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash -E \
    && apt-get install -y symfony-cli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN ln -sf /usr/bin/symfony /usr/local/bin/symfony

ARG PUID=1000
ARG PGID=1000
RUN groupadd -g ${PGID} appgroup && useradd -u ${PUID} -g appgroup -m appuser

RUN sed -i 's/#force_color_prompt=yes/force_color_prompt=yes/g' /etc/skel/.bashrc \
    && echo 'PS1="\[\e[36m\]\u\[\e[m\]@\[\e[32m\]docker-php\[\e[m\]:\[\e[34m\]\w\[\e[m\]\$ "' >> /etc/skel/.bashrc \
    && echo "alias ls='ls --color=auto'" >> /etc/skel/.bashrc \
    && echo "alias ll='ls -la'" >> /etc/skel/.bashrc \
    && cp /etc/skel/.bashrc /home/appuser/.bashrc \
    && chown appuser:appgroup /home/appuser/.bashrc

RUN echo "error_log = /var/log/php-fpm.log" > /usr/local/etc/php/conf.d/logging.ini
RUN touch /var/log/php-fpm.log && chown appuser:appgroup /var/log/php-fpm.log
RUN chown -R appuser:appgroup /var/log/supervisor /var/run
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

# ==========================================
# STAGE 3: builder
# ==========================================
FROM base AS builder

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN composer run-script post-install-cmd --no-interaction || true \
    && php bin/console cache:clear --env=prod \
    && php bin/console cache:warmup --env=prod

# ==========================================
# STAGE 4: production
# ==========================================
FROM php:8.5-fpm AS production


RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4t64 \
    libxml2 \
    libonig5 \
    libicu76 \
    libssl3t64 \
    librabbitmq \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=base /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /var/www/test3

ARG PUID=1000
ARG PGID=1000
RUN groupadd -g ${PGID} appgroup && useradd -u ${PUID} -g appgroup -m appuser

COPY --from=builder --chown=appuser:appgroup /var/www/test3 /var/www/test3

USER appuser

CMD ["php-fpm"]
