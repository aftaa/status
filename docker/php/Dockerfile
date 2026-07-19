# ==========================================
# STAGE 1: base (Общий фундамент для dev и builder)
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
# STAGE 2: dev (Окружение для локальной разработки)
# ==========================================
FROM base AS dev

# Сюда ставим symfony-cli и утилиты, нужные ТОЛЬКО при разработке
# Вместо старого метода ставим через официальный шелл-скрипт,
# но принудительно переносим бинарник в /usr/local/bin
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash -E \
    && apt-get install -y symfony-cli \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Добавляем железную гарантию: проверяем, где бинарник, и делаем ссылку в /usr/local/bin
RUN ln -sf /usr/bin/symfony /usr/local/bin/symfony

ARG PUID=1000
ARG PGID=1000
RUN groupadd -g ${PGID} appgroup && useradd -u ${PUID} -g appgroup -m appuser

# === Настройки цветного bash для appuser ===
# Включаем поддержку цвета в терминале force_color_prompt=yes
RUN sed -i 's/#force_color_prompt=yes/force_color_prompt=yes/g' /etc/skel/.bashrc \
    # Меняем стандартную строку приглашения (PS1) на красивую и яркую
    && echo 'PS1="\[\e[36m\]\u\[\e[m\]@\[\e[32m\]docker-php\[\e[m\]:\[\e[34m\]\w\[\e[m\]\$ "' >> /etc/skel/.bashrc \
    # Добавляем привычные алиасы для подцветки списков файлов (ls, ll)
    && echo "alias ls='ls --color=auto'" >> /etc/skel/.bashrc \
    && echo "alias ll='ls -la'" >> /etc/skel/.bashrc \
    # Копируем настроенный конфиг в домашнюю папку нашего appuser
    && cp /etc/skel/.bashrc /home/appuser/.bashrc \
    && chown appuser:appgroup /home/appuser/.bashrc

RUN echo "error_log = /var/log/php-fpm.log" > /usr/local/etc/php/conf.d/logging.ini
RUN touch /var/log/php-fpm.log && chown appuser:appgroup /var/log/php-fpm.log
RUN chown -R appuser:appgroup /var/log/supervisor /var/run
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Код сюда не копируем, подразумевается монтирование папки через volumes в docker-compose
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

# ==========================================
# STAGE 3: builder (Сборка продакшн-вендоров и кэша)
# ==========================================
FROM base AS builder

# Копируем зависимости и ставим их без dev-пакетов
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader --no-scripts

# Копируем весь код проекта
COPY . .

# Прогреваем кэш Symfony (для продакшна symfony-cli не нужен, стандартного php bin/console достаточно)
RUN composer run-script post-install-cmd --no-interaction || true \
    && php bin/console cache:clear --env=prod \
    && php bin/console cache:warmup --env=prod

# ==========================================
# STAGE 4: production (Максимально чистый рантайм)
# ==========================================
FROM php:8.5-fpm AS production

# Ставим ТОЛЬКО легковесные рантайм-библиотеки (без заголовков -dev)
RUN apt-get update && apt-get install -y --no-install-recommends \
    libcurl4t64 \
    libxml2 \
    libonig5 \
    libicu76 \
    libssl3t64 \
    librabbitmq \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Копируем конфигурацию opcache
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Переносим скомпилированные PHP-расширения из этапа base
COPY --from=base /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=base /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

WORKDIR /var/www/test3

# Создаем непривилегированного пользователя для запуска прода
ARG PUID=1000
ARG PGID=1000
RUN groupadd -g ${PGID} appgroup && useradd -u ${PUID} -g appgroup -m appuser

# Забираем из билдера ГОТОВЫЙ код с вендорами и кэшем и СРАЗУ даем на него права нашему пользователю
COPY --from=builder --chown=appuser:appgroup /var/www/test3 /var/www/test3

USER appuser

CMD ["php-fpm"]
