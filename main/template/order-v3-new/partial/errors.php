<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity\Order\Product $product
) { ?>

    <? if (count($order->errors) == 0) return null; ?>

    <? if ($product instanceof \Model\OrderDelivery\Entity\Order\Product
        && (bool)array_filter($order->errors, function(\Model\OrderDelivery\Error $error) { return $error->code == 708; })
    ) :
        $errors = array_filter($order->errors, function(\Model\OrderDelivery\Error $error) use ($product) { return $error->code == 708 && $error->details['product_id'] == $product->id; });
        if ((bool)$errors) $error = reset($errors);
        ?>
        <? if (isset($error) && $error->details['product_id'] == $product->id) : ?>

        <div class="order-error order-error--warning">Вы хотели <?= $error->details['requested_amount'] ?> шт. Есть только <?= $error->details['max_available_quantity'] ?> шт.<i class="order-error__closer js-order-err-close"></i></div>
    <? endif; ?>
    <? endif; ?>
<? } ?>