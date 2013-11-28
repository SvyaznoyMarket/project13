<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик'
) {
    $class = \View\Id::cartButtonForProduct($product->getId() . '-oneClick') . ' jsOneClickButton ' . $class;

    if ($product->isInShopStockOnly()) {
        $class .= ' mShopsOnly';
    } elseif ($product->isInShopShowroomOnly()) {
        $class .= ' mShopsOnly';
    }

    if (!$product->getIsBuyable()) {
        $url = '#';
        $class .= ' mDisabled';
        $value = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет в наличии';
    } else if (!isset($url)) {
        $urlParams = [
            'productId' => $product->getId(),
        ];
        if ($helper->hasParam('sender')) {
            $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
        }
        $url = $helper->url('cart.oneClick.product.set', $urlParams);
    }

?>

    <div class="btnOneClickBuy">
        <a class="btnOneClickBuy__eLink" href="<?= $url ?>" class="<?= $class ?>" data-group="<?= $product->getId() ?>"><?= $value ?></a>
    </div>

<? };