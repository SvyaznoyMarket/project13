<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string $url
 * @param array $form
 * @param \Model\Order\Entity|null $order
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $url,
    array $form,
    \Model\Order\Entity $order = null
) {
    // validation
    if (!$url || !$form) {
        return '';
    }
?>

<form action="<?= $url ?>" method="post">
    <? foreach ($form as $key => $value): ?>
        <input name="<?= $key ?>" value="<?= $value ?>" type="hidden" />
    <? endforeach ?>

    <button id="pay-button" type="submit" class="orderPayment_btn btn3">Оплатить</button>
</form>

<? }; return $f;