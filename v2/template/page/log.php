<?php

return function ($page) { ?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $page['id'] ?> - Журнал</title>

    <meta id="js-enter-debug" name="enter-debug" content="<?= $page['dataDebug'] ?>">
    <meta id="js-enter-version" name="enter-version" content="<?//= $page['dataVersion'] ?>">
    <meta id="js-enter-module" name="enter-module" content="default">
    <link rel="stylesheet" href="/v2/css/global.min.css">

    <script data-main="/v2/js/main.js" src="/v2/js/vendor/require-2.1.14.js"></script>
</head>
<body>
<h1><?= $page['id'] ?></h1>

<p><?= $page['date'] ?></p>

<ul>
    <? foreach ($page['messages'] as $message): ?>
        <li style="background: <?= $message['color'] ?>">
            <hr />
            <a href="/log/<?= $page['id'] ?>#log-<?= $message['id'] ?>">&#35; ссылка</a>
            <pre id="log-<?= $message['id'] ?>"><?= $message['value'] ?></pre>
        </li>
    <? endforeach ?>
</ul>

</body>
</html>

<? } ?>
