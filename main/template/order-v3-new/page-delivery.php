<?php
/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\OrderDelivery\Entity $orderDelivery
 * @param null $error
 * @param \Model\User\Address\Entity[] $userAddresses
 * @param \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition
 * @param \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $error = null,
    array $userAddresses = [],
    \Model\OrderDelivery\UserInfoAddressAddition $userInfoAddressAddition = null,
    array $userEnterprizeCoupons = []
) {
?>
    <div class="order__wrap js-order-wrapper">
        <?= $helper->render('order-v3-new/partial/delivery/content', [
            'orderDelivery' => $orderDelivery,
            'error' => $error,
            'userAddresses' => $userAddresses,
            'userInfoAddressAddition' => $userInfoAddressAddition,
            'userEnterprizeCoupons' => $userEnterprizeCoupons,
        ]) ?>
    </div>
<? };