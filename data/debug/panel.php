<?php
$debug = App::debug();
$page = new \View\Layout();

if (isset($response) && (200 != $response->getStatusCode())) {
    $debug->add('status', $response->getStatusCode(), 150, \Debug\Collector::TYPE_ERROR);
}

if ((bool)\App::exception()->all()) {
    $debug->add('error', implode("\n", array_map(function($e) { return (string)$e; }, \App::exception()->all())), 149, \Debug\Collector::TYPE_ERROR);
}

$debug->add('id', \App::$id, 145);
$debug->add('env', \App::$env, 144);
$debug->add('git.branch', shell_exec(sprintf('cd %s && git rev-parse --abbrev-ref HEAD', realpath(\App::config()->appDir))), 143);
$debug->add('git.tag', shell_exec(sprintf('cd %s && git describe --always --tag', realpath(\App::config()->appDir))), 142);

$action =implode('.', \App::request()->attributes->get('action', array()));
$debug->add('act', $action ?: 'undefined', 138, $action ? \Debug\Collector::TYPE_INFO : \Debug\Collector::TYPE_ERROR);

if (\App::user()->getToken()) {
    $debug->add('user', \App::user()->getToken(), 135);
}

// timers
$appTimer = \Debug\Timer::get('app');
$coreTimer = \Debug\Timer::get('core');
$contentTimer = \Debug\Timer::get('content');

$debug->add('time.app', sprintf('%s ms', round($appTimer['total'] - $coreTimer['total'] - $contentTimer['total'], 3) * 1000), 98);
$debug->add('time.core', sprintf('%s ms [%s]', round($coreTimer['total'], 3) * 1000, $coreTimer['count']), 97);
$debug->add('time.content', sprintf('%s ms [%s]', round($contentTimer['total'], 3) * 1000, $contentTimer['count']), 96);
$debug->add('time.total', sprintf('%s ms', round($appTimer['total'], 3) * 1000), 95);

// memory
$debug->add('memory', sprintf('%s Mb', round(memory_get_peak_usage() / 1048576, 2)), 90);

// session
if ('local' == \App::$env) {
    $debug->add('session', json_encode(\App::session()->all(), JSON_PRETTY_PRINT), 89);
}

$requestLogger = \Util\RequestLogger::getInstance();
$requestData = $requestLogger->getStatistics();
$requestData = json_decode($requestData, true);
if (!isset($requestData['api_queries'])) $requestData = array('api_queries' => array());
$queryString = '';
foreach ((array)$requestData['api_queries'] as $query) {
    $queryString .=
        (round($query['time'], 3) * 1000)
        . ' ' . '<span style="color: #cccccc;">' . $query['host'] . '</span>'
        . ' ' . '<a style="color: #00ffff" href="' . $page->escape($query['url']) . '" target="_blank" data-method="' . ((bool)$query['post'] ? 'post' : 'get') . '">' . $page->escape(rawurldecode($query['url'])) . '</a>'
        . ' ' . ((bool)$query['post'] ? json_encode($query['post'], JSON_UNESCAPED_UNICODE) : '')
        . '<br />';
}
$debug->add('query', $queryString, 80);

if (!\App::request()->isXmlHttpRequest()) {
?>
    <span draggable="true" style="position: fixed; bottom: 30px; left: 2px; z-index: 999; background: #000000; color: #11ff11; opacity: 0.9; padding: 4px 6px; border-radius: 5px; font-size: 11px; font-weight: normal; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <span onclick="$(this).parent().remove()" style="cursor: pointer; font-size: 16px; color: #999999;" title="закрыть">&times;</span>
        <br />

    <? foreach ($debug->getAll() as $item) { ?>
        <?
            $isHidden = mb_strlen($item['value']) > 40;
            if ($isHidden) $item['value'] = '<pre>' . $item['value'] . '</pre>';
        ?>
        <span style="color: #ffffff"><?= $item['name'] ?>:</span>

        <? if ($isHidden) { ?>
            <span onclick="var el = $(this).next(); el.is(':hidden') ? el.show() : el.hide()" style="cursor: pointer; color: #00ffff;">...</span>
        <? } ?>

        <span<? if ($isHidden) { ?> style="display: none;" <? } ?>>
            <? if (\Debug\Collector::TYPE_ERROR == $item['type']) { ?><span style="color: #ff0000;"><?= $item['value'] ?></span><? } else { ?><?= $item['value'] ?><? } ?>
        </span>
        <br />
    <? } ?>
    </span>
<?
}
