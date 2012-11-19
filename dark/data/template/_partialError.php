<?php
/**
 * @var $sender string
 * @var $e      \Exception
 */
?>

<? if (\App::config()->debug): ?>
    <div style="min-width: 50px; min-height: 20px; max-width: 400px; max-height: 200px; overflow: auto; z-index: 999; background: #000000; color: #ff0000; opacity: 0.9; padding: 4px 6px; border-radius: 5px; font-size: 11px; font-weight: normal; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
        <span onclick="$(this).parent().remove()" style="cursor: pointer; font-size: 16px; color: #999999;" title="закрыть">&times;</span>
        <br />
        <span style="color: #ffffff;"><?= $sender ?></span><br />
        #<?= $e->getCode() ?> <?= $e->getMessage() ?>
    </div>
<? endif ?>
