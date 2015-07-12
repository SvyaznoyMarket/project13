## Lite

#### Структура проекта

Проект частично использует кодовую базу project13.

**PHP**

Контроллеры в проекте используются из `main/controller`.
Если соответствующий View содержится в папке `lite/view`, то будет использован он с рендерингом шаблонов из `lite/template`.

**Javascript и CSS**

Исходные файлы проекта располагаются в папке `frontend` и собираются в `web/public` с помощью отдельного `Gruntfile.js`.
Для сборки сборки и работы над проектом в папке `frontend` необходимо выполнить команды

```
npm install
grunt all
```

Для автоматической сборки проекта при `git pull|checkout` можно записать в [хук](http://git-scm.com/docs/githooks)
`.git/hooks/post-checkout` и `.git/hooks/post-merge`  следующие строки:

```
# building project "lite"
if [ -f frontend/Gruntfile.js ]
then
    cd frontend && grunt all
fi
```

Для корректной работы source maps необходимо в конфигурацию nginx прописать следующий `location`:

```
location ^~ /frontend {
    root /www/enter/wwwroot;
    try_files $uri =404;
}
```

В проекте используется модульная система [Yandex Modules](https://github.com/ymaps/modules)

- Модульная система и определения модулей находятся в папке `frontend/js/modules` и сжимаются
в `web/public/js/modules.js` - почти единственный js, который импортируется в тело страницы.
- В папке `frontend/js/library` находятся небольшие хелперы, которые сжимаются
в `web/public/js/library.js` - модуль `library`.
- В папке `frontend/js/plugins` находятся плагины для jQuery, которые по отдельности сжимаются
в папку `web/public/plugins` - модули `jquery.*`.
- В папке `frontend/js/library` находятся "init"-скрипты для шаблонов (от которых неплохо бы уйти).
