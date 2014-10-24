<?php
/**
 * @var $page             \View\Layout
 * @var $cart             \Session\Cart
 * @var $creditEnabled    bool
 * @var $products         \Model\Product\CartEntity[]
 * @var $services         \Model\Product\Service\Entity[]
 * @var $cartProductsById \Model\Cart\Product\Entity[]
 * @var $cartServicesById \Model\Cart\Service\Entity[]
 * @var $productKitsById  \Model\Product\CartEntity[]
 */
?>

<?
$creditData = [];

foreach ($products as $product) {
    $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;
    if (!$cartProduct) {
        \App::logger()->error(sprintf('Товар #%s не найден в корзине', $product->getId()));
        continue;
    }

    $creditData[] = array(
        'id'       => $product->getId(),
        'quantity' => $cartProduct->getQuantity(),
        'price'    => $product->getPrice(),
        'type'     => \Model\CreditBank\Repository::getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null),
    );
}
?>


<!-- Basket -->
<? if ($creditEnabled): ?>
    <div id="tsCreditCart" data-value="<?= $page->json($creditData) ?>"></div>
<? endif ?>


<div id="kitPopup" class="popup">
    <a class="close" href="#">Закрыть</a>
    <div class="bKitPopup"></div>
    <!-- ko foreach: kitPopupItems -->
    <div class="bKitPopupLine clearfix">
        <div class="bKitPopupLine_eImg fl"><img src="" alt="" data-bind="attr: { src: $data.image, alt: $data.name }"/></div>
        <div class="bKitPopupLine_eName fl" data-bind="text: $data.name"></div>
        <div class="bKitPopupLine_ePrice fl"><!-- ko text: window.printPrice($data.price) --><!-- /ko --> <span class="rubl">p</span></div>
        <div class="bKitPopupLine_eQuan fl"><!-- ko text: $data.quantity --><!-- /ko --> шт.</div>
    </div>
    <!-- /ko -->
</div>

<? foreach ($products as $product): ?>
<?
    $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;
    $categoryId = isset($categoryIdByProductId[$product->getId()]) ? $categoryIdByProductId[$product->getId()] : null;
    if (!$cartProduct) continue;
?>
    <div class="basketLine basketline clearfix" ref="<?= $product->getId() ?>" data-product-id="<?= $product->getId() ?>" data-category-id="<?= $categoryId ?>">
        <div class="basketLine__img">
            <a class="basketLine__imgLink" href="<?= $product->getLink() ?>">
                <img src="<?= $product->getImageUrl() ?>" alt="<?= $product->getName() ?>" />
            </a>
        </div>

        <div class="basketLine__desc">
            <div class="basketLine__desc__name">
                <a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a>
                <noindex>
                <? if ($cartProduct->getIsBuyable()): ?>
                    <div class="basketLine__desc__available">Есть в наличии</div>
                <? else: ?>
                    <div class="basketLine__desc__available">Нет в наличии</div>
                <? endif ?>
                </noindex>
            </div>

            <div class="basketLine__desc__info basketinfo">
                <div class="descPriceLine">
                    <div class="descPriceOne">
                        <span class="price one"><?= $product->getPrice() ? $page->helper->formatPrice($product->getPrice()) : '' ?></span>
                        <span class="rubl">p</span>
                    </div>
                    <div class="descCount">
                        <?= $page->render('_spinner', array(
                            'quantity' => $cartProduct->getQuantity(),
                            'incUrl'   => $page->url('cart.product.set', array('productId' => $product->getId(), 'quantity' => $cartProduct->getQuantity() + 1)),
                            'decUrl'   => $page->url('cart.product.set', array('productId' => $product->getId(), 'quantity' => $cartProduct->getQuantity() - 1)),
                        ))?>
                    </div>
                </div>

                <div class="descPrice">
                    <span class="price sum"><?= $page->helper->formatPrice($cartProduct->getPrice() * $cartProduct->getQuantity()) ?></span> <span class="rubl">p</span>
                    <a href="<?= $page->url('cart.product.delete', array('productId' => $product->getId())) ?>" class="button whitelink js-basketLineDeleteLink-<?= $page->escape($product->getId()) ?>">Удалить</a>
                </div>
            </div>

            <?
                $kitData = [];
                foreach ($product->getKit() as $kit) {
                    $productKit = isset($productKitsById[$kit->getId()]) ? $productKitsById[$kit->getId()] : null;
                    if (!$productKit) {
                        \App::logger()->error(sprintf('Не загружен товар для элемента набора #%s', $kit->getId()));
                        continue;
                    }

                    $kitData[] = array(
                        'name'     => $productKit->getName(),
                        'image'    => $productKit->getImageUrl(0),
                        'price'    => $productKit->getPrice(),
                        'quantity' => $kit->getCount() * $cartProduct->getQuantity(),
                    );
                }
            ?>
            <div class="clear pb15"></div>

            <? if ((bool)$kitData): ?>
                <a id="<?= sprintf('product-%s-kit', $product->getId()) ?>" href="#" class="product_kit-data fr mt15 button whitelink" data-value="<?= $page->json($kitData) ?>">Посмотреть состав набора</a>
            <? endif ?>

        </div>
    </div>
<? endforeach ?>

<!-- /Basket -->

<div class="jsKnockoutCart" style="display: none" data-bind="visible: ajaxProducts().length > 0">

    <!-- ko foreach: ajaxProducts -->

    <div class="basketLine basketline clearfix" ref="" data-product-id="" data-category-id="" data-bind="">
        <div class="basketLine__img">
            <a class="basketLine__imgLink" href="" data-bind="attr: { href: $data.product.link }">
                <img src="" alt="" data-bind="attr: { src: $data.product.img, alt: $data.product.name}">
            </a>
        </div>

        <div class="basketLine__desc">
            <div class="basketLine__desc__name">
                <a href="" data-bind="text: $data.product.name"></a>
                <noindex><div class="basketLine__desc__available">Есть в наличии</div></noindex>
            </div>

            <div class="basketLine__desc__info basketinfo">
                <div class="descPriceLine">
                    <div class="descPriceOne">
                        <span class="price one" data-bind="text: window.printPrice($data.product.price)"></span>
                        <span class="rubl">p</span>
                    </div>
                    <div class="descCount">

                        <div class="numerbox">
                            <a href="" data-bind="attr: { href: '/cart/add-product/' + $data.product.id + '?quantity=' + ($data.product.quantity - 1) }"><b class="ajaless" title="Уменьшить"></b></a>
                            <input maxlength="2" class="ajaquant" value="" data-bind="value: $data.product.quantity">
                            <a href="" data-bind="attr: { href: '/cart/add-product/' + $data.product.id + '?quantity=' + ($data.product.quantity + 1) }"><b class="ajamore" title="Увеличить"></b></a>
                        </div>                    </div>
                </div>

                <div class="descPrice">
                    <span class="price sum" data-bind="text: window.printPrice($data.product.price * $data.product.quantity)"></span> <span class="rubl">p</span>
                    <a href="" class="button whitelink js-basketLineDeleteLink-61596" data-bind="attr: { href: '/cart/delete-product/' + $data.product.id }">Удалить</a>
                </div>
            </div>

            <div class="clear pb15"></div>

        </div>
    </div>

    <!-- /ko -->

</div>