<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $class = '',
    $data = []
) {

    $buttonText = 'Купить';

    // класс для набор-пакетов
    if ($product->getKit() && !$product->getIsKitLocked()) $class .= ' btn-kit ';

    ?>

    <a
        class="goods__btn btn-primary js-buy-button <?= $class ?>"
        href="<?= $helper->url('cart.product.set', ['productId' => $product->getId()]) ?>"
        ><?= $buttonText ?></a>

<? }; return $f;