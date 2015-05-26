<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\Entity
 * @var $isHidden      bool
 * @var $kit           \Model\Product\Kit\Entity
 * @var $addInfo       array
 **/
?>

<?php
$isHidden = isset($isHidden) && $isHidden;
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
$addInfo = isset($addInfo)?$addInfo:[];
?>

<div class="goodsbox goodsline bNewGoodsBox <? echo ($isHidden)? 'hidden': '' ?> js-goodsbox">
    <div class="goodsboxlink" <? if ($product->getIsBuyable()): ?> data-cid="<?= $product->getId() ?>" <? endif ?> <?= (count($addInfo)) ? 'data-add="'.$page->json($addInfo).'"' :''; ?>>
        <div class="photo">
            <? if ($product->hasVideo()): ?>
                <a class="goodsphoto_eVideoShield goodsphoto_eVideoShield_small" href="<?= $product->getLink() ?>"></a>
            <? endif ?>

            <? if ($product->has3d()): ?>
                <a style="right:<?= $product->hasVideo() ? '42' : '0' ?>px;" class="goodsphoto_eGrad360 goodsphoto_eGrad360_small" href="<?= $product->getLink() ?>"></a>
            <? endif ?>

            <a href="<?= $product->getLink() ?>">
                <? if ($label = $product->getLabel()): ?>
                    <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $page->escape($label->getName()) ?>"/>
                <? endif ?>

                <img height="160" width="160" title="<?= $page->escape($product->getName()) ?>" alt="<?= $page->escape($product->getName()) ?>" src="<?= $product->getMainImageUrl('product_120') ?>" class="mainImg"/>
            </a>
        </div>
        <div class="info">
            <div class="h3"><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></div>
            <span class="gray bNGB__eArt mInlineBlock">
                Артикул #<?= $product->getArticle() ?>

                <span class="bNGB__eDrop"><a href="<?= $product->getLink() ?>" style="display: none"></a></span>
            </span>

            <? if (\App::config()->product['reviewEnabled']): ?>
                <?= $page->render('product/_reviewsStarsCompact', ['product' => $product]) ?>
            <? endif ?>

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
            <? if ($product->getPriceOld()): ?>
                <p class="font16 crossText"><span class="price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></p>
            <? endif ?>
            <span class="db font18 pb10"><b><span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></b></span>

            <?= \App::closureTemplating()->render('cart/__button-product', ['product' => $product]) ?>
            <?= $page->render('product/show/__corner_features', ['product' => $product]) ?>
            <? if ($product->getIsBuyable() && 5 != $product->getStatusId()): ?>
            <noindex>
                <ul class="bNGB__eUl">
                    <li><strong class="orange">Есть в наличии</strong></li>
                </ul>
            </noindex>
            <? endif ?>
        </div>
    </div>
</div>
