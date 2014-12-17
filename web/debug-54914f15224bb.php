<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Debug</title>

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
            $errors[] = new \ErrorException($message, 0, $level, $file, $line);

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

    $before = isset($_GET['before']) ? (int)$_GET['before'] : 100;

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
            if (!$line) continue;

            $line = json_decode($line, true);
            if (isset($line['date'])) {
                if (!isset($page['date'])) $page['date'] = $line['date'];
                unset($line['date']);
            }

            // query
            if (isset($line['query']['response'])) {
                $line['query']['response'] = json_decode($line['query']['response']);
            }

            $messages[] = [
                'id'    => $line['time'],
                'color' => $id == $line['_id'] ? '#ffffcc' : '#ededed',
                'value' => json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
            ];
        }
    }

    die(var_dump($messages));

} catch (\Exception $e) {
    if (404 == $e->getCode()) {
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    throw $e;
}

?>

<body>

<div class="container" id="content">
    <? foreach ($errors as $error): ?>
        <div class="alert alert-<?= in_array($error->getSeverity(), [2, 8]) ? 'warning' : 'danger' ?>" role="alert">
            <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
            <?= $error->getMessage() ?>
        </div>
    <? endforeach ?>


</div>

</body>
</html>