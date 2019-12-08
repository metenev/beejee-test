# beejee-test

Чтобы запустить этот проект, понадобится файл .env в корне с вот таким содержимым:

```
# Путь к корневой директории сайта
# (нужен, если сайт находится во вложенной директории)
ROOT_PATH=

# Доступ к БД
DB_HOST=
DB_PORT=
DB_NAME=
DB_USER=
DB_PASSWORD=

# Число заданий на стрнаице
TASKS_PAGE_SIZE=3
```
А также в настройках веб-сервера нужно указать путь до него в переменной `ENV_DIR`. Путь должен указывать на директорию, в которой лежит этот файл, а не на сам файл.

Для NGinx вот так:
```
location ~* \.php {
    # ...настройки

    fastcgi_param ENV_DIR "<...>";
}
```

А для Apache вот так:
```
SetEnv ENV_DIR <...>
```
