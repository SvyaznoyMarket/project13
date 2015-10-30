<?php
return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $class = null,
    $value = 'Купить быстро в 1 клик',
    \Model\Shop\Entity $shop = null,
    $sender = [],
    $sender2 = '',
    $location = ''
) {
    if (
        !$product->getIsBuyable()
        || (!$shop && ($product->getPartnersOffer() /* SITE-3877 */ || $product->getKit() || ($product->getKit() && !$product->getIsKitLocked())))
        || 5 === $product->getStatusId() // SITE-2924
        || (\App::abTest()->isOrderMinSumRestriction() && $product->getPrice() < \App::config()->minOrderSum)
    ) {
        return;
    }

    $data = [
        'productId' => $product->id,
        'productUi' => $product->getUi(),
        'shop'      => $shop ? $shop->getId() : null,
        'url'       => $product->getLink() . '#one-click' . ($shop ? '-' . $shop->getId() : ''),
        'class'     => $class . ' jsOneClickButton',
        'text'      => $value,
        'sender'    => $helper->json($sender),
        'sender2'   => $sender2,
        'location'  => $location
    ];

    if ('product-card' === $location) {
        $data['class'] .= ' js-oneClickButton-main';
    }
?>
    <!--noindex-->
        <?= $helper->renderWithMustache('product-page/_buyButtonOneClick', $data) ?>
    <!--/noindex-->
<?php };