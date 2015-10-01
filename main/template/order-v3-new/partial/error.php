<?php

/**
 * @param $error
 * @param \Model\OrderDelivery\Entity|null $orderDelivery
 */
$f = function(
    $error,
    \Model\OrderDelivery\Entity $orderDelivery = null
) {

    if (!is_array($error)) $error = (array)$error;

    /** @var \Model\OrderDelivery\Error[] $deliveryErrors */
    $deliveryErrors = [];
    if ($orderDelivery) {
        foreach ($orderDelivery->errors as $e) {
            if ($e->isMaxQuantityError() && !isset($e->details['block_name'])) {
                $deliveryErrors[md5($e->message)] = $e;
            }
        }
    }
?>

    <? foreach ($error as $e) : ?>
        <div id="OrderV3ErrorBlock" class="errtx" style="display: <?= $e ? 'block' : 'none'?>">
            <?= $e ?>
        </div>
    <? endforeach ?>

    <? if (!$orderDelivery instanceof \Model\OrderDelivery\Entity) return ?>

    <? foreach ($deliveryErrors as $e) : ?>
        <div class="order-error order-error--warning">
            <?= $e->message ?>
            <i class="order-error__closer js-order-err-close"></i>
        </div>
    <? endforeach ?>

<? }; return $f;