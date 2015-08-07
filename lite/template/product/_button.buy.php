<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $class = '',
    $data = []
) {

    $buttonText = 'Купить';
    $jsClass = 'js-buy-button';
    $link = $helper->url('cart.product.set', ['productId' => $product->getId()]);

    // классы для набор-пакетов
    if ($product->getKit() && !$product->getIsKitLocked()) {
        $kitParams = [];
        $class .= ' btn-kit ';
        $jsClass = 'js-buy-kit-button';
        foreach ($product->getKit() as $kitItem) {
            $kitParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }
        $link = $helper->url('cart.product.setList', $kitParams);
    }

    if ($product->getSlotPartnerOffer()) {
        $class .= ' btn-set ';
        $buttonText = 'Отправить заявку';
    }

    ?>

    <a
        class="goods__btn btn-primary <?= $jsClass ?> <?= $class ?>"
        href="<?= $link ?>"
        ><?= $buttonText ?></a>

<? }; return $f;