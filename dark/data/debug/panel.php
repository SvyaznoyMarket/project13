<?php
$debug = App::debug();

if (isset($response) && (200 != $response->getStatusCode())) {
    $debug->add('status', $response->getStatusCode(), \Debug\Collector::TYPE_ERROR, 101);
}

$debug->add('env', \App::config()->env, \Debug\Collector::TYPE_INFO, 100);
$debug->add('act', implode('.', \App::request()->attributes->get('action', array('?', '?'))), 99);

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

if (!\App::request()->isXmlHttpRequest()) {
?>
    <div draggable="true" style="position: absolute; top: 24px; left: 2px; width: 260px; overflow: hidden; z-index: 999; background: #000000; color: #00ff00; opacity: 0.8; padding: 4px 6px; border-radius: 5px; font-size: 10px; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <span onclick="$(this).parent().remove()" style="cursor: pointer; font-size: 16px; color: #ffffff;">&times;</span>
        <span onclick="var el = $(this).parent(); if (el.height() > 40) { el.animate({height: 16}, 200); } else { el.animate({height: 300}, 200); }" style="cursor: pointer; font-size: 16px; color: #ffffff;">&minus;</span>
        <span onclick="var el = $(this).parent(); if (el.width() > 260) { el.animate({width: 260}, 200); } else { el.animate({width: 1024}, 200); }" style="cursor: pointer; font-size: 16px; color: #ffffff;">+</span>
        <br />

    <? foreach ($debug->getAll() as $item) { ?>
        <?
            $isHidden = mb_strlen($item['value']) > 40;
            if ($isHidden) $item['value'] = '<pre>' . $item['value'] . '</pre>';
        ?>
        <?= $item['name'] ?>:

        <? if ($isHidden) { ?>
        <span onclick="var el = $(this).next(); el.is(':hidden') ? el.show() : el.hide()" style="cursor: pointer;">...</span>
        <? }?>

        <span<? if ($isHidden) { ?> style="display: none;" <? } ?>>
            <? if (\Debug\Collector::TYPE_ERROR == $item['type']) { ?><span style="color: #ff0000;"><?= $item['value'] ?></span><? } else { ?><?= $item['value'] ?><? } ?>
        </span>
        <br />
    <? } ?>
    </div>
<?
}
