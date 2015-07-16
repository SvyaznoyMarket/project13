<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $data = []
) {

    $buttonText = 'Купить';

    ?>

    <a
        class="goods__btn btn-primary js-module-require"
        data-module="enter.buyButton"
        href="<?= $helper->url('cart.product.set', ['productId' => $product->getId()]) ?>"
        ><?= $buttonText ?></a>

<? }; return $f;