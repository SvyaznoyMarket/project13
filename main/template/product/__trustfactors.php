<?php
return function (\Helper\TemplateHelper $helper, $trustfactors, $type) {
?>
    <? foreach ((array)$trustfactors as $trustfactor): ?>
        <? if ($trustfactor['type'] === $type): ?>
            <div class="trustfactor-<?= $type ?>">
                <? if (isset($trustfactor['link'])): ?>
                    <a href="<?= $helper->escape($trustfactor['link']) ?>">
                <? endif ?>

                <? if ('image' === $trustfactor['media']['provider']): ?>
                    <? foreach ($trustfactor['media']['sources'] as $source): ?>
                        <? if ('original' === $source['type']): ?>
                            <img src="<?= $helper->escape($source['url']) ?>" width="<?= $helper->escape($source['width']) ?>" height="<?= $helper->escape($source['height']) ?>" alt="<?= $helper->escape($trustfactor['alt']) ?>" />
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>

                <? if (isset($trustfactor['link'])): ?>
                    </a>
                <? endif ?>
            </div>
        <? endif ?>
    <? endforeach ?>
<?
};