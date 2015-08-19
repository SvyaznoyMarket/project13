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
            <div class="errtx" style="display: block">
                <?= $e->message ?>
            </div>
        <? endif ?>
    <? endforeach ?>

<? } ?>