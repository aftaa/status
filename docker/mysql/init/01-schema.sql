-- Создаём базу данных, если её нет (на всякий случай)
CREATE DATABASE IF NOT EXISTS task_manager;

-- Создаём пользователя (если не существует)
CREATE USER IF NOT EXISTS 'task_manager'@'%' IDENTIFIED BY 'task_manager';

-- Даём права на базу
GRANT ALL PRIVILEGES ON task_manager.* TO 'task_manager'@'%';

-- Применяем права
FLUSH PRIVILEGES;

-- Переключаемся на базу task_manager
USE task_manager;

-- Создаём таблицу tasks
-- CREATE TABLE IF NOT EXISTS tasks (
--     id INT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     name VARCHAR(255) NOT NULL,
--     is_completed BOOLEAN DEFAULT FALSE,
--     created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
-- );
