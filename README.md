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

### Релиз проекта

Сборкой для релиза занимается [Jenkins](http://ci.ent3.ru) при появлении новых коммитов в ветке *master*. 
Если к последнему коммиту не привязан тэг, то Jenkins не сможет создать архив для релиза. Для автоматического создания 
архива необходимо пушить изменения с созданной тэгой на последнем коммите.

Если события произошли не по этому сценарию, то необходимо протегировать последний коммит, зайти
в [проект на Jenkins](http://ci.ent3.ru/job/site-prod/) и нажать на ссылку "Собрать сейчас".