<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\ExpandedEntity
 * @var $productVideos \Model\Product\Video\Entity[]
 **/
?>

<?php
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
if (!isset($productVideos)) $productVideos = [];
?>

<style type="text/css">
    .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small, .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small:hover {
        background: url('/css/item/img/videoStiker_small.png') no-repeat 0 0;
        right: -55px;
        top: 130px;
    }
</style>

<div class="goodsbox goodsline bNewGoodsBox" ref="<?= $product->getToken() ?>">
    <div class="goodsboxlink" <? if ($product->getIsBuyable()): ?> data-cid="<?= $product->getId() ?>" <? endif ?>>
        <div class="photo">
            <? if ((bool)$productVideos && (bool)\App::config()->abtest['enabled'] && \App::abTest()->getCase()->getKey() == 'video'): ?><a class="goodsphoto_eVideoShield goodsphoto_eVideoShield_small" href="<?= $product->getLink() ?>"></a><? endif ?>
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
                <?php if (!(bool)\App::config()->abtest['enabled'] || 'comment' !== \App::abTest()->getCase()->getKey()): ?>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;"></span>', round($product->getRating())) ?>
                <?= str_repeat('<span class="ratingview" style="width:13px;vertical-align:middle;display:inline-block;background-position:-51px 0;"></span>', 5 - round($product->getRating())) ?>
                <?php endif; ?>

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
            <? if ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
            <p class="font16 crossText"><span class="price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></p>
            <? endif ?>
            <span class="db font18 pb10"><b><span class="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <span class="rubl">p</span></b></span>

            <div class="goodsbar mSmallBtns">
                <?= $page->render('cart/_button', array('product' => $product, 'disabled' => !$product->getIsBuyable())) ?>
            </div>
            <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
                <div class="notBuying font12">
                    <div class="corner"><div></div></div>
                    Только в магазинах
                </div>
            <? endif ?>
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
