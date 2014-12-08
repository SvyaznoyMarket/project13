<?php

return function (
    \Helper\TemplateHelper $helper,
    $trustfactors,
    $type
) {

?>

    <? foreach ((array)$trustfactors as $trustfactor): ?>
        <? if ($trustfactor['type'] === $type): ?>
            <div class="trustfactor-<?= $type ?>">
                <? if ('image' === $trustfactor['media']['provider']): ?>
                    <? if (isset($trustfactor['link'])): ?>
                        <a id="trustfactor-<?= $type ?>-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($trustfactor['link']) ?>">
                    <? endif ?>

                    <? foreach ($trustfactor['media']['sources'] as $source): ?>
                        <? if ('original' === $source['type']): ?>
                            <img src="<?= $helper->escape($source['url']) ?>" width="<?= $helper->escape($source['width']) ?>" height="<?= $helper->escape($source['height']) ?>" alt="<?= $helper->escape($trustfactor['alt']) ?>" />
                            <? break ?>
                        <? endif ?>
                    <? endforeach ?>

                    <? if (isset($trustfactor['link'])): ?>
                        </a>
                    <? endif ?>
                <? elseif ('file' === $trustfactor['media']['provider']): ?>
                    <? foreach ($trustfactor['media']['sources'] as $source): ?>
                        <? if ('original' === $source['type']): ?>
                            <a id="trustfactor-<?= $type ?>-<?= md5(json_encode([$trustfactor])) ?>" href="<?= $helper->escape($source['url']) ?>" target="_blank" class="trustfactor-<?= $type ?>-file"><span><?= $helper->escape($trustfactor['alt']) ?></span></a>
                            <? break ?>
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>
            </div>
        <? endif ?>
    <? endforeach ?>

<? };