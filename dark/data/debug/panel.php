<?php
$debug = App::debug();

if (isset($response) && (200 != $response->getStatusCode())) {
    $debug->add('status', $response->getStatusCode(), 150, \Debug\Collector::TYPE_ERROR);
}

$debug->add('id', \App::$id, 140);
$debug->add('env', \App::config()->env, 139);
$debug->add('act', implode('.', \App::request()->attributes->get('action', array('?', '?'))), 138);

// timers
$appTimer = \Debug\Timer::get('app');
$coreTimer = \Debug\Timer::get('core');
$contentTimer = \Debug\Timer::get('content');

$debug->add('app', sprintf('%s s', round($appTimer['total'] - $coreTimer['total'] - $contentTimer['total'], 3)), 98);
$debug->add('core', sprintf('%s s [%s]', round($coreTimer['total'], 3), $coreTimer['count']), 97);
$debug->add('content', sprintf('%s s [%s]', round($contentTimer['total'], 3), $contentTimer['count']), 96);
$debug->add('total', sprintf('%s s', round($appTimer['total'], 3)), 95);

// memory
$debug->add('memory', sprintf('%s Mb', round(memory_get_peak_usage() / 1048576, 2)), 90);

$requestLogger = \Util\RequestLogger::getInstance();
$requestData = $requestLogger->getStatistics();
$requestData = json_decode($requestData, true);
if (!isset($requestData['api_queries'])) $requestData = array('api_queries' => array());
$queryString = '';
foreach ((array)$requestData['api_queries'] as $query) {
    $queryString .=
        sprintf('%01.3f', round($query['time'], 3))
        . ' ' . '<a style="color: #00ffff" href="' . $query['url'] . '" target="_blank" data-method="' . ((bool)$query['post'] ? 'post' : 'get') . '">' . $query['url'] . '</a>'
        . ' ' . ((bool)$query['post'] ? print_r($query['post'], true) : '')
        . '<br />';
}
$debug->add('api', $queryString, 80);

if (!\App::request()->isXmlHttpRequest()) {
?>
    <span draggable="true" style="position: absolute; top: 24px; left: 2px; z-index: 999; background: #000000; color: #00ff00; opacity: 0.8; padding: 4px 6px; border-radius: 5px; font-size: 11px; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <span onclick="$(this).parent().remove()" style="cursor: pointer; font-size: 16px; color: #999999;" title="закрыть">&times;</span>
        <br />

    <? foreach ($debug->getAll() as $item) { ?>
        <?
            $isHidden = mb_strlen($item['value']) > 40;
            if ($isHidden) $item['value'] = '<pre>' . $item['value'] . '</pre>';
        ?>
        <span style="color: #ffffff"><?= $item['name'] ?></span>:

        <? if ($isHidden) { ?>
        <span onclick="var el = $(this).next(); el.is(':hidden') ? el.show() : el.hide()" style="cursor: pointer; color: #00ffff;">...</span>
        <? }?>

        <span<? if ($isHidden) { ?> style="display: none;" <? } ?>>
            <? if (\Debug\Collector::TYPE_ERROR == $item['type']) { ?><span style="color: #ff0000;"><?= $item['value'] ?></span><? } else { ?><?= $item['value'] ?><? } ?>
        </span>
        <br />
    <? } ?>
    </span>
<?
}
