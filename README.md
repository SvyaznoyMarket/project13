### Сборка проекта

**.js и .css**

Исходные файлы проекта располагаются в папке `web/css`, `web/styles`, `web/js/dev` и собираются с помощью `Gruntfile.js`.
Для сборки и работы над проектом в корневой папке необходимо выполнить команды

```
npm install
grunt all
```

Для автоматической сборки проекта при `git pull|checkout` можно записать в [хук](http://git-scm.com/docs/githooks)
`.git/hooks/post-checkout` и `.git/hooks/post-merge`  следующие строки:

```
# building project "lite"
if [ -f Gruntfile.js ]
then
    grunt all
fi
```