# Установка Docker и docker-compose

## Установка в Ubuntu Linux

Официальные инструкции:

- Docker CE для Ubuntu: https://docs.docker.com/engine/install/ubuntu/#install-docker-ce
- Docker Compose V1: https://docs.docker.com/compose/install/other/

Рекомендуется добавить текущего пользователя в группы docker и www-data, чтобы:

1. Использовать команду `docker` без `sudo`
2. Предоставлять доступ к файлам проекта пользователю `www-data` в docker-контейнере

Добавление в группы:

```bash
sudo usermod -a -G docker $USER
sudo usermod -a -G www-data $USER
```

Эффект наступит после Sign Out / Sign In либо перезагрузки.

## Настройка проекта

```bash
# Собрать образ
docker-compose build

# Запустить контейнеры в фоновом режиме
docker-compose up -d

# Проверить состояние контейнеров
docker-compose ps

# Запустить bash в контейнере с php и установить зависимости
docker\bin\wiki-backend-app-bash
composer install
exit

# Инициализировать базу данных (версия для Linux)
docker exec -i wiki-backend-db mysql -pVIzP6LTScyYy <data/init.sql && echo OK
docker exec -i wiki-backend-tests-db mysql -proh6shiD <data/init.sql && echo OK

# Инициализировать базу данных (версия для Windows)
docker\bin\wiki-backend-init-db
```

## Использование

```bash
# Запустить контейнеры в фоновом режиме
docker-compose up -d

# Проверить состояние контейнеров
docker-compose ps

# Смотреть логи контейнеров (Ctrl+C для остановки)
docker-compose logs -f

# Открыть сессию bash в контейнере
docker/bin/wiki-backend-app-bash

# Остановить контейнеры
docker-compose down --remove-orphans

```

Чистка данных:

```bash
# УДАЛИТЬ ВСЕ ДАННЫЕ локальной базы данных (находятся в docker volume)
docker volume rm 4_wiki_backend_wiki_backend_db_data
```

## Использование XDebug

Для отладки используйте XDebug:

1. Docker-контейнеры настраивать не надо, в них xdebug уже включён и настроен на подключение к локальному хосту
2. Для браузера установите расширение XDebug Helper
    - Chrome: https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc
    - Firefox: https://addons.mozilla.org/en-US/firefox/addon/xdebug-helper-for-firefox/
3. В своей IDE настройте работу с XDebug, используя любую инструкцию для docker-контейнеров
    - Для PhpStorm раздел "PHP > Servers" уже настроен как полагается
