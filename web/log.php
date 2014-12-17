<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log <?= date('m.d H:i:s') ?></title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <script src="http://yastatic.net/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
</head>

<?php

ini_set('display_errors', true);
ini_set('short_open_tag', true);
error_reporting(-1);

try {
    /** @var \ErrorException[] $errors */
    $errors = [];

    // Обработчик ошибок
    set_error_handler(function($level, $message, $file, $line) use (&$errors) {
        static $levels = [
            E_WARNING           => 'Warning',
            E_NOTICE            => 'Notice',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
        ];

        switch ($level) {
            case E_USER_ERROR: case E_NOTICE: case E_WARNING:
            $errors[md5($message)] = new \ErrorException($message, 0, $level, $file, $line);

            return true;
        }

        if (error_reporting() & $level) {
            throw new \ErrorException(sprintf('%s: %s in %s line %d', isset($levels[$level]) ? $levels[$level] : $level, $message, $file, $line));
        }

        return false;
    });

    // Активность приложения
    $enabled = (bool)(@$_SERVER['APPLICATION_DEBUG_ENABLED']);
    if (true !== $enabled) {
        throw new \Exception('Приложение неактивно', 404);
    }

    // Директория журналов
    $logDir = @$_SERVER['APPLICATION_LOG_DIR'];
    if (!is_readable($logDir)) {
        throw new \Exception('Директория журналов недоступна. Проверь параметр APPLICATION_LOG_DIR');
    }

    // Ид строки журнала
    $id = @$_GET['id'];
    if (empty($id)) {
        throw new \Exception('Не указан параметр id (идентификатор строки журнала)');
    }

    // Файлы журналов
    $logFilenames = @$_GET['file'];
    if (!(bool)$logFilenames) {
        throw new \Exception('Не указан параметр file (название файла-журнала)', 400);
    }
    if (!is_array($logFilenames)) {
        $logFilenames = [$logFilenames];
    }

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 100000;

    $before = isset($_GET['before']) ? (int)$_GET['before'] : null;

    // Проверка доступности файлов журналов
    $logFiles = [];
    foreach ($logFilenames as $i => $logFilename) {
        $logFile = rtrim($logDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $logFilename;
        if (!is_readable($logFile)) {
            trigger_error(new \Exception(sprintf('Файл журналов недоступен %s', $logFile)), E_USER_ERROR);
            continue;
        }

        $logFiles[] = $logFile;
    }

    $parentLink = null;

    // Поиск строк журнала по ид
    $messages = [];
    foreach ($logFiles as $logFile) {
        $command = sprintf('tail -n %s %s | grep "\"_id\":\"%s\""%s',
            $offset,
            $logFile,
            $id,
            $before ? (' -B ' . $before) : ''
        );

        $result = shell_exec($command);

        foreach (explode(PHP_EOL, $result) as $line) {
            $lineId = md5($line);
            $line = json_decode($line, true);
            if (!$line) continue;

            $line += ['message' => null];

            // Игнорировать
            if (
                'Create curl' == $line['message']
                || 'Curl execute' == $line['message']
                || 'End curl executing' == $line['message']
                || (bool)array_intersect(['view'], $line['_tag'])
            ) {
                continue;
            }


            $icon = 'comment';

            // query
            if (isset($line['query']['response'])) {
                $line['query']['response'] = json_decode($line['query']['response']);
            }

            if (!$parentLink && ($id == $line['_id']) && $line['_parent']) {
                $parentLink = '?' . http_build_query([
                    'id'   => $line['_parent'],
                    'file' => $logFilenames,
                ]);
            }

            $title = $line['_id'];
            $color = '#ABABAB';
            if (isset($line['url'])) {
                $url = parse_url($line['url']) + ['host' => null, 'path' => null];

                $title = $url['host'] . $url['path'];
                $icon = 'volume-up';

                if (0 === strpos($url['host'], 'scms')) {
                    $color = '#FF9933';
                } else if (0 === strpos($url['host'], 'search')) {
                    $color = '#3366FF';
                } else if (0 === strpos($url['host'], 'api')) {
                    $color = '#66CC33';
                } else {
                    $color = '#686868';
                }
            } else if (in_array('router', $line['_tag'])) {
                $title = urldecode($line['uri']);
                $icon = 'globe';
            } else if (isset($line['core.response'])) {
                $title = 'Ответ от ядра';
            } else if ($line['message']) {
                $title = $line['message'];
            } else {
                $color = '#EDEDED';
            }

            if ('error' == $line['_type']) {
                $icon = 'fire';
            }

            $parentId = $line['_parent'];

            $value = $line;
            unset(
                //$value['_id'],
                $value['_parent'],
                //$value['_time'],
                $value['_type'],
                $value['_tag']
            );

            $messages[] = [
                'id'     => $lineId,
                'parent' => $parentId,
                'title'  => $title,
                'type'   => $line['_type'],
                'icon'   => $icon,
                'color'  => $color,
                'value'  => json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ];
        }
    }

} catch (\Exception $e) {
    if (404 == $e->getCode()) {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    throw $e;
}

?>

<body>

<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <span class="navbar-brand">Log</span>
        </div>

        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <? if ($parentLink): ?>
                    <li><a href="<?= $parentLink ?>">Parent</a></li>
                <? endif ?>
            </ul>
            <form class="navbar-form navbar-left" role="search">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Search">
                </div>
                <button type="submit" class="btn btn-default" disabled>Search</button>
            </form>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>


<div class="container" id="content">
    <? foreach ($errors as $error): ?>
        <div class="alert alert-<?= in_array($error->getSeverity(), [2, 8]) ? 'warning' : 'danger' ?>" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <?= $error->getMessage() ?>
        </div>
    <? endforeach ?>

    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <? foreach ($messages as $message): ?>
            <div class="panel panel-default">
                <div class="panel-heading" role="tab" id="h-<?= $message['id'] ?>">
                    <h4 class="panel-title">
                        <a id="log-<?= $message['id'] ?>" href="#log-<?= $message['id'] ?>"><span aria-hidden="true" class="glyphicon glyphicon-<?= $message['icon'] ?>"<? if ($message['color']): ?> style="color: <?= $message['color'] ?>" <? endif ?>></span></a>
                        <a data-toggle="collapse" data-parent="#accordion" href="#c-<?= $message['id'] ?>" aria-expanded="false" aria-controls="c-<?= $message['id'] ?>">
                            <?= $message['title'] ?>
                        </a>
                    </h4>
                </div>

                <div id="c-<?= $message['id'] ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                    <div class="panel-body">
                        <pre><?= $message['value'] ?></pre>
                    </div>
                </div>
            </div>
        <? endforeach ?>
    </div>
</div>

</body>
</html>