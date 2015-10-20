<?php
/**
 * @var $page                 \View\User\FavoritesPage
 * @var $helper               \Helper\TemplateHelper
 * @var $user                 \Session\User
 * @var $productsByUi         \Model\Product\Entity[]
 * @var $product              \Model\Product\Entity|null
 * @var $favoriteProductsByUi \Model\Favorite\Product\Entity[]
 */
?>

<div class="personalPage">
    <?= $page->render('user/_menu', ['page' => $page]) ?>
    <div class="personalTitle">Избранное</div>

    <div class="table-favorites table table--border-cell-hor">
        <? if (!$favoriteProductsByUi): ?>
            У вас нет избранных товаров
        <? endif ?>

        <? foreach ($favoriteProductsByUi as $favoriteProduct): ?>
        <?
            if (!$product = @$productsByUi[$favoriteProduct->ui]) continue;
            $rowId = 'id-favoriteRow-' . $product->getUi() ?: uniqid();
        ?>

            <div class="table-row <?= $rowId ?>">
                <div class="table-favorites__cell-left table-cell">
                    <a href="<?= $product->getLink() ?>">
                        <img src="<?= $product->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getName()) ?>" class="table-favorites__img">
                    </a>
                </div>

                <div class="table-favorites__cell-center table-cell">
                    <a href="<?= $product->getLink() ?>" class="table-favorites__name"><?= $helper->escape($product->getName()) ?></a>
                    <? if ($product->getPrice()): ?>
                        <div class="table-favorites__price"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl">p</span></div>
                    <? endif ?>
                </div>

                <div class="table-favorites__cell-right table-cell">
                    <? if ($product->getIsBuyable()) : ?>
                        <?= $helper->render('cart/__button-product', [
                            'product'  => $product,
                            'onClick'  => isset($addToCartJS) ? $addToCartJS : null,
                            'noUpdate'  => true,
                            //'sender'   => $buySender + ['from' => preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER')) == null ? $request->server->get('HTTP_REFERER') : preg_filter('/\?+?.*$/', '', $request->server->get('HTTP_REFERER'))],
                            //'sender2'  => $buySender2,
                            'location' => 'user-favorites',
                        ]);// Кнопка купить ?>
                    <? else : ?>
                        Нет в наличии
                    <? endif ?>

                    <!--<div class="btnBuy"><a href="" class="btn-type btn-type--buy js-orderButton jsBuyButton">В корзину</a></div>-->
                    <div class="table-favorites__delete">
                        <a data-ajax="true" href="<?= $helper->url('favorite.delete', ['productUi' => $product->getUi()]) ?>" class="jsFavoriteDeleteLink undrl" data-target=".<?= $rowId ?>">Удалить</a>
                    </div>
                </div>
            </div>
        <? endforeach ?>
    </div>
</div>
