## Отладка

> Сначала настройте XDebug по инструкции из курса в iSpring Learn

Для отладки тестов, запускаемых PHPUnit, нужно активировать режим отладки с помощью переменных окружения

В Windows выполните в консоли две команды:

```bash
set XDEBUG_CONFIG="idekey=123"
set PHP_IDE_CONFIG=serverName=localhost
```

На Linux выполните в консоли другие две команды:

```bash
export XDEBUG_CONFIG="idekey=123"
export PHP_IDE_CONFIG=serverName=localhost
```

После этого _в той же консоли_ запустите тесты, которые вы планируете отлаживать.

Пример:

```bash
composer functional-test

```
