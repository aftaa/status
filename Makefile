# Переменные для удобства (если изменятся названия файлов, поменяем только тут)
COMPOSE_PROD = docker compose -f docker-compose.prod.yml
PHP_CONTAINER = php

.PHONY: help up down restart build logs migrate cache deploy

# Команда по умолчанию (показывает список доступных команд)
help:
	@echo "Доступные команды для продакшн-окружения:"
	@echo "  make up       - Запустить контейнеры в фоне (detach)"
	@echo "  make down     - Остановить и удалить контейнеры"
	@echo "  make restart  - Перезапустить проект"
	@echo "  make build    - Собрать прод-образы с нуля БЕЗ кэша Docker"
	@echo "  make logs     - Посмотреть логи всех сервисов"
	@echo "  make migrate  - Прокатать миграции базы данных Doctrine"
	@echo "  make cache    - Очистить и прогреть кэш Symfony"
	@echo "  make deploy   - ПОЛНЫЙ ЦИКЛ ДЕПЛОЯ (Сборка, подъем, миграции, кэш)"

# Базовые операции с Docker Compose
up:
	$(COMPOSE_PROD) up -d

down:
	$(COMPOSE_PROD) down

restart: down up

build:
	$(COMPOSE_PROD) build --no-cache

logs:
	$(COMPOSE_PROD) logs -f

# Команды для работы с Symfony внутри контейнера
migrate:
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

cache:
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console cache:clear --env=prod
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console cache:warmup --env=prod

# =====================================================================
# 🔥 Главная кнопка: Автоматический деплой «All-in-One»
# =====================================================================
deploy:
	@echo "=== 🚀 Запуск процесса деплоя на продакшене ==="
	@echo "1. Сборка свежих образов (без кэша)..."
	$(COMPOSE_PROD) build --no-cache

	@echo "2. Перезапуск контейнеров..."
	$(COMPOSE_PROD) down
	$(COMPOSE_PROD) up -d

	@echo "3. Ожидание запуска MySQL перед миграциями (пауза 5 сек)..."
	@sleep 5

	@echo "4. Применение миграций базы данных..."
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction

	@echo "5. Финальный прогрев кэша Symfony..."
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console cache:clear --env=prod
	$(COMPOSE_PROD) exec $(PHP_CONTAINER) php bin/console cache:warmup --env=prod

	@echo "=== 🎉 Деплой успешно завершен! Проект готов к работе ==="
