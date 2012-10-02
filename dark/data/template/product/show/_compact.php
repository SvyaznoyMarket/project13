<?php
/**
 * @var $page \View\DefaultLayout
 * @var $product \Model\Product\Entity
 * @var $index
 * */
?>

<?php
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && count($product->getModel()->getPropertyList());
?>

<div class="goodsbox"<?= (isset($index, $maxPerPage) && ($index > $maxPerPage)) ? ' style="display:none;"' : '' ?>>

    <div class="photo">
        <a href="<?= $product->getLink() ?>">
            <? if (!empty($kit) && $kit->getQuantity()) { ?>
            <div class="bLabelsQuantity" src="/images/quantity_shild.png"><?= $kit->getQuantity(); ?> шт.</div>
            <? } ?>
            <? if ($label = $product->getMainLabel()) { ?>
            <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
            <? } ?>
            <?= $title = $product->getName(); // @todo create getTitle method, or check is category isset ?>
            <? if ($product->getMainCategory()) $title .= ' - ' . $product->getMainCategory()->getName();?>
            <img class="mainImg" src="<?= $product->getMediaImageUrl(2) ?>" alt="<?= $title ?>" title="<?= $title ?>" width="160" height="160"/>
        </a>
    </div>

    <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating())) ?>
    <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating())) ?>

    <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>

    <div class="font18 pb10 mSmallBtns">
        <span class="price"><?= formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span>
    </div>

    <? if ($hasModel): ?>
    <a href="<?= $product->getLink() ?>">
        <div class="bListVariants">
            Доступно в разных вариантах<br>
            (<?= $product->getModel()->getVariations() ?>)
        </div>
    </a>
    <? endif ?>

    <!-- Hover -->
    <div class="boxhover"<? if ($product->getIsBuyable()): ?> ref="<?= $product->getToken() ?>"<? endif ?>>
        <b class="rt"></b><b class="lb"></b>

        <div class="rb">
            <div class="lt" data-url="<?= $product->getLink() ?>">
                <!--<a href="" class="fastview">Быстрый просмотр</a>-->
                <div class="photo">
                    <a href="<?= $product->getLink() ?>">
                        <? if (!empty($kit) && $kit->getQuantity()) { ?>
                        <div class="bLabelsQuantity" src="/images/quantity_shild.png"><?= $kit->getQuantity(); ?>
                            шт.
                        </div>
                        <? } ?>
                        <? if ($label = $product->getMainLabel()) { ?>
                        <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
                        <? } ?>
                        <img class="mainImg" src="<?= $product->getMediaImageUrl(2) ?>" alt="<?= $title ?>" title="<?= $title ?>" width="160" height="160"/>
                    </a>
                </div>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating())) ?>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating())) ?>
                <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>

                <div class="font18 pb10 mSmallBtns">
                    <span class="price"><?= formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span>
                </div>
                <? if ($hasModel): ?>
                <a href="<?= $product->getLink() ?>">
                    <div class="bListVariants">
                        Доступно в разных вариантах<br>
                        (<?= $product->getModel()->getVariations() ?>)
                    </div>
                </a>
                <? endif ?>
            </div>
        </div>
    </div>
    <!-- /Hover -->

</div>
