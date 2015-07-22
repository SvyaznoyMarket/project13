<?
/**
 * @var $product \Model\Product\Entity
 */
$helper = \App::helper();
?>

<!-- параплашка -->
<div class="header header_fix js-userbar-fixed js-module-require" data-module="enter.userbar" style="display: none">
    <div class="wrapper table">
        <div class="header__side header__logotype table-cell">
            <a href="/" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line_top">
                <ul class="bread-crumbs bread-crumbs_mini">
                    <li class="bread-crumbs__item"><a href="/catalog/electronics" class="bread-crumbs__link underline">Электроника</a></li>
                    <li class="bread-crumbs__item">Игры и консоли</li>
                </ul>
            </div>

            <div class="header__line header__line_bottom">

                <!-- карточка товара -->
                <div class="header-buy">
                    <div class="header-buy__product header-buy__cell">
                        <div class="header-buy__product-img header-buy__cell"><img src="<?= $product->getMainImageUrl('product_120') ?>" alt="" class="image"></div>
                        <div class="header-buy__product-name header-buy__cell"><?= $product->getName() ?></div>
                    </div>

                    <div class="header-buy__price header-buy__cell">
                        <? if ($product->getPriceOld()) : ?><div class="goods__price-old"><span class="line-through"><?= $helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl-css">P</span></div><? endif ?>
                        <div class="goods__price-now"><?= $helper->formatPrice($product->getPrice()) ?> <span class="rubl-css">P</span></div>
                    </div>

                    <div class="header-buy__btn header-buy__cell">
                        <?= $helper->render('product/_button.buy', [
                            'product' => $product,
                            'class'   => 'btn-primary_middle'
                        ]) ?>
                    </div>
                </div>
                <!--/ карточка товара -->

                <ul class="user-controls">

                    <?= $page->render('common/userbar/_compare') ?>
                    <?= $page->render('common/userbar/_user') ?>

                </ul>
            </div>
        </div>

        <?= $page->render('common/userbar/_cart') ?>

    </div>
</div>
<!--/ параплашка -->
