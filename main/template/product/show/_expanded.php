<?php
/**
 * @var $page          \View\Layout
 * @var $product       \Model\Product\ExpandedEntity
 * @var $productVideos \Model\Product\Video\Entity[]
 * @var $addInfo       array
 **/
?>

<?php
$hasModel = (isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty();
if (!isset($productVideos)) $productVideos = [];
$addInfo = isset($addInfo)?$addInfo:[];
?>

<style type="text/css">
    .goodsbox .photo .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small,
    .goodsbox .photo .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small:hover {
        background: url('/css/item/img/videoStiker_small.png') no-repeat 0 0;
        right: 0;
        top: 130px;
        width: 42px;
        height: 35px;
    }
</style>

<div class="goodsbox goodsline bNewGoodsBox" ref="<?= $product->getToken() ?>">
    <div class="goodsboxlink" <? if ($product->getIsBuyable()): ?> data-cid="<?= $product->getId() ?>" <? endif ?> <?php if (count($addInfo)) print 'data-add="'.$page->json($addInfo).'"'; ?>>
        <div class="photo">
            <? if ((bool)$productVideos): ?><a class="goodsphoto_eVideoShield goodsphoto_eVideoShield_small" href="<?= $product->getLink() ?>"></a><? endif ?>
            <a href="<?= $product->getLink() ?>">
                <? if ($label = $product->getLabel()): ?>
                    <img class="bLabels" src="<?= $label->getImageUrl() ?>" alt="<?= $page->escape($label->getName()) ?>"/>
                <? endif ?>

                <img height="160" width="160" title="<?= $page->escape($product->getName()) ?>" alt="<?= $page->escape($product->getName()) ?>" src="<?= $product->getImageUrl() ?>" class="mainImg"/>
            </a>
        </div>
        <div class="info">
            <h3><a href="<?= $product->getLink() ?>"><?= $product->getName() ?></a></h3>
            <span class="gray bNGB__eArt mInlineBlock">
                Артикул #<?= $product->getArticle() ?>

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
