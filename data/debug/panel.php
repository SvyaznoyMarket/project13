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
$debug->add('name', \App::$name, 143);
$debug->add('git.branch', shell_exec(sprintf('cd %s && git rev-parse --abbrev-ref HEAD', realpath(\App::config()->appDir))), 142);
$debug->add('git.tag', shell_exec(sprintf('cd %s && git describe --always --tag', realpath(\App::config()->appDir))), 141);

$action =implode('.', (array)\App::request()->attributes->get('action', []));
$debug->add('act', $action ?: 'undefined', 138, $action ? \Debug\Collector::TYPE_INFO : \Debug\Collector::TYPE_ERROR);
$debug->add('route', \App::request()->attributes->get('route'), 137);

if (\App::user()->getToken()) {
    $debug->add('user', \App::user()->getToken(), 135);
}

// server
if ('live' != \App::$env) {
    $debug->add('server', json_encode(isset($_SERVER) ? $_SERVER : [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 134);
}

// session
$debug->add('session', json_encode(\App::session()->all(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 133);

// memory
$debug->add('memory', sprintf('%s Mb', round(memory_get_peak_usage() / 1048576, 2)), 132);

// timers
$appTimer = \Debug\Timer::get('app');
$coreTimer = \Debug\Timer::get('core');
$contentTimer = \Debug\Timer::get('content');
$dataStoreTimer = \Debug\Timer::get('data-store');

$debug->add('time.app', sprintf('%s ms', round($appTimer['total'] - $coreTimer['total'] - $dataStoreTimer['total'] - $contentTimer['total'], 3) * 1000), 98);
$debug->add('time.core', sprintf('%s ms [%s]', round($coreTimer['total'], 3) * 1000, $coreTimer['count']), 97);
$debug->add('time.data-store', sprintf('%s ms [%s]', round($dataStoreTimer['total'], 3) * 1000, $dataStoreTimer['count']), 96);
$debug->add('time.content', sprintf('%s ms [%s]', round($contentTimer['total'], 3) * 1000, $contentTimer['count']), 95);
$debug->add('time.total', sprintf('%s ms', round($appTimer['total'], 3) * 1000), 94);


// ab test
if ((bool)\App::config()->abtest['enabled']) {
    $options = '<span style="color: #cccccc;">Тестирование проводится до </span><span style="color: #00ffff;">' . date('d-m-Y H:i', strtotime(\App::config()->abtest['bestBefore'])) . '</span><br />';
    foreach (\App::abTest()->getOption() as $option) {
        $options .= '<span style="color: #' . ($option->getKey() == \App::abTest()->getCase()->getKey() ? 'color: #11ff11' : 'cccccc') . ';">' . $option->getTraffic() . ($option->getTraffic() === '*' ? ' ' : '% ') . $option->getKey() . ' ' . $option->getName() . '</span><br />';
    }
}
$debug->add('abTest', $options, 89);

// log
if ('live' != \App::$env) {
    $debug->add('log', '<a style="color: #00ffff" href="/debug/log/' . \App::$id . '" onclick="var el = $(this); $.post(el.attr(\'href\'), function(response) { el.html(\'\'); el.after(\'<pre>\' + response + \'</pre>\'); el.next(\'pre\').css({\'color\': \'#ffffff\', \'max-height\': \'300px\', \'max-width\': \'1200px\', \'overflow\': \'auto\'}) }); return false">...</a>', 88);
}

$requestLogger = \Util\RequestLogger::getInstance();
$requestData = $requestLogger->getStatistics();
$requestData = json_decode($requestData, true);
if (!isset($requestData['api_queries'])) $requestData = ['api_queries' => []];
$queryString = '';
foreach ((array)$requestData['api_queries'] as $query) {
    $queryString .=
        (round($query['time'], 3) * 1000)
        . ' ' . '<span style="color: #cccccc;">' . $query['host'] . '</span>'
        . ' ' . '<a class="curl-link" style="color: #00ffff" href="' . $query['url'] . '" target="_blank" data-data="' . $page->escape((bool)$query['post'] ? json_encode($query['post'], JSON_UNESCAPED_UNICODE) : '') . '" onclick="var el = $(this); if (el.next(\'.curl-response:first\').text().length) { el.next(\'.curl-response:first\').html(\'\'); return false; }; el.next(\'.curl-response:first\').html(\'...\'); $.post(\'\/debug\/curl\', {\'url\': el.attr(\'href\'), \'data\': el.data(\'data\')}, function(data) { el.next(\'.curl-response:first\').html(data) }); return false">' . $page->escape(rawurldecode($query['url'])) . '</a>'
        . ' ' . ((bool)$query['post'] ? json_encode($query['post'], JSON_UNESCAPED_UNICODE) : '')
        . ' ' . '<span class="curl-response"></span>'
        . '<br />';
}
$debug->add('query', $queryString, 80);

if (!\App::request()->isXmlHttpRequest()) {
?>
    <span style="position: fixed; bottom: 30px; left: 2px; z-index: 999; background: #000000; color: #11ff11; opacity: 0.9; padding: 4px 6px; border-radius: 5px; font-size: 11px; font-weight: normal; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <span onclick="$(this).parent().remove()" style="cursor: pointer; font-size: 16px; color: #999999;" title="закрыть">&times;</span>
        <span onclick="window.location.replace('<?= $page->helper->replacedUrl(['APPLICATION_DEBUG' => 0]) ?>')" style="cursor: pointer; font-size: 16px; color: #999999;" title="отключить">■</span>
        <br />

    <? foreach ($debug->getAll() as $item) { ?>
        <?
            $isHidden = mb_strlen(strip_tags($item['value'])) > 40;
            if ($isHidden) $item['value'] = '<pre>' . $item['value'] . '</pre>';
        ?>
        <span style="color: #ffffff"><?= $item['name'] ?>:</span>

        <? if ($isHidden) { ?>
            <span onclick="var el = $(this).next(); el.is(':hidden') ? el.css('display', 'block') : el.css('display', 'none')" style="cursor: pointer; color: #00ffff;">...</span>
        <? } ?>

        <span<? if ($isHidden) { ?> style="display: block; display: none; max-height: 600px; max-width: 1200px; overflow: auto;" <? } ?>>
            <? if (\Debug\Collector::TYPE_ERROR == $item['type']) { ?><span style="color: #ff0000;"><?= $item['value'] ?></span><? } else { ?><?= $item['value'] ?><? } ?>
        </span>
        <br />
    <? } ?>
    </span>
<?
}
