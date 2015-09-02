<?php
return function(
    $orderDelivery,
    $error
) {

    if (!is_array($error)) $error = (array) $error;

?>

    <? foreach ($error as $e) : ?>
        <div id="OrderV3ErrorBlock" class="errtx" style="display: <?= $e ? 'block' : 'none'?>">
            <?= $e ?>
        </div>
    <? endforeach ?>

    <? if (!$orderDelivery instanceof \Model\OrderDelivery\Entity) return ?>

    <? foreach ($orderDelivery->errors as $e) : ?>
        <? if ($e->isMaxQuantityError() && !isset($e->details['block_name'])) : ?>
            <div class="order-error order-error--warning">
                <?= $e->message ?>
                <i class="order-error__closer js-order-err-close"></i>
            </div>
        <? endif ?>
    <? endforeach ?>

<? } ?>