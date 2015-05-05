<?php
return function(
    \Helper\TemplateHelper $helper,
    $orderDelivery,
    $error
) {
?>

    <div id="OrderV3ErrorBlock" class="errtx" style="display: <?= $error ? 'block' : 'none'?>">
        <?= $error ?>
    </div>

    <? if (!$orderDelivery instanceof \Model\OrderDelivery\Entity) return ?>

    <? foreach ($orderDelivery->errors as $e) : ?>
        <? if ($e->isMaxQuantityError() && !isset($e->details['block_name'])) : ?>
            <div class="errtx" style="display: block">
                <?= $e->message ?>
            </div>
        <? endif ?>
    <? endforeach ?>

<? } ?>