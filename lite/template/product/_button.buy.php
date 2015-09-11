<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $class = '',
    $data = []
) {
?>
    <? if ($product->isAvailable()): ?>
        <?
        $buttonText = 'Купить';
        $jsClass = 'js-buy-button';
        $link = $helper->url('cart.product.set', ['productId' => $product->getId()]);
        $onclick = null;
        $dataAttrs = [];

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

        // заявка (слот)
        if ($product->getSlotPartnerOffer()) {
            $class .= ' btn-set ';
            $buttonText = 'Отправить заявку';
            $jsClass = 'js-buy-slot-button';
        }

        // на витрине
        if (!$product->getIsBuyable() && $product->isInShopShowroomOnly()) {
            $jsClass = null;
            $link = '';
            $buttonText = 'На витрине';
            $class .= ' btn-in-showroom jsShowDeliveryMap';
            $onclick = 'return false;';
            $dataAttrs['product-ui'] = $product->getUi();
            $dataAttrs['product-id'] = $product->getId();
        }

        ?>

        <a
            class="goods__btn btn-primary <?= $jsClass ?> <?= $class ?>"
            href="<?= $link ?>"
            <? if ($onclick) : ?>onclick="<?= $onclick ?>"<? endif ?>
            <? foreach ($dataAttrs as $key => $value) : ?>
                data-<?= $key ?>="<?= $value ?>"
            <? endforeach ?>
            ><?= $buttonText ?></a>
    <? else: ?>
        <span>Нет в наличии</span>
    <? endif ?>

<? }; return $f;