<?php
/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity\Order|null $order
 * @param int $orderCount
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $order = null,
    $orderCount = 1
) {
    $buttonLocation = \App::abTest()->getOrderButtonLocation();

    $cssClass = '';

    if('fixed' === $buttonLocation){
        $cssClass = 'order-agreement_fix-btn js-fixBtn';
    }else if('top' === $buttonLocation){
        $cssClass = 'order-agreement_top';
    }
?>
<div class="order-agreement <?=$cssClass?>">
    <?= \App::templating()->render('order-v3/common/_blackfriday', ['version' => 2]) ?>
    <div class="order-agreement__check" data-field-container="accept">
        <input type="checkbox" class="customInput customInput-checkbox js-customInput jsAcceptAgreement" id="accept" name="" data-field="accept" value="" required="required" />

        <label  class="customLabel customLabel_sure customLabel-checkbox jsAcceptTerms" for="accept">
            <span class="customLabel__sure">*</span>
            Я ознакомлен и согласен<br><span class="order-agreement__oferta js-order-oferta-popup-btn" data-value="<?= $order->seller->offer ?>" >с информацией о продавце и его офертой</span>
        </label>
    </div>
    <button class="btn-type btn-type--buy btn-type--order" type="submit" form="js-orderForm">Оформить</button>
</div>

<? }; return $f;