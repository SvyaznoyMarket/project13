<?php
/**
 * @var $page    \View\DefaultLayout
 * @var $product \Model\Product\ExpandedEntity
 * */
?>

<?php
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
?>

<div class="goodsbox goodsline bNewGoodsBox">
    <div class="goodsboxlink" <? if ($product->getIsBuyable()): ?> ref="<?= $product->getToken() ?>" data-cid="<?= $product->getId() ?>" <? endif ?>>
        <div class="photo">
            <a href="<?= $product->getLink() ?>">
                <? if ($label = $product->getLabel()): ?>
                    <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $label->getName() ?>"/>
                <? endif ?>

                <img height="160" width="160" title="<?= $product->getName() ?>" alt="<?= $product->getName() ?>" src="<?= $product->getImageUrl() ?>" class="mainImg"/>
            </a>
        </div>
        <div class="info">
            <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
            <span class="gray bNGB__eArt mInlineBlock">
                Артикул #<?= $product->getArticle() ?>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating())) ?>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating())) ?>

                <span class="bNGB__eDrop"><a href="<?= $product->getLink() ?>" style="display: none"></a></span>
            </span>

            <div class="pb5 bNGB__eDesc">
                <?php foreach ($product->getProperty() as $property): ?>
                <?= $property->getName() ?>: <?= $property->getStringValue() ?><br/>
                <?php endforeach ?>
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
        <div class="extrainfo">
            <span class="db font18 pb10"><b><span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></b></span>

            <div class="goodsbar mSmallBtns">
                <?= $page->render('cart/_button', array('product' => $product)) ?>
            </div>

            <? if ($product->getIsBuyable()): ?>
            <noindex>
                <ul class="bNGB__eUl">
                    <li><strong class="orange">Есть в наличии</strong></li>
                </ul>
            </noindex>
            <? endif ?>
        </div>
    </div>
</div>
