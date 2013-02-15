<?php
/**
 * @var $page    \Terminal\View\Product\IndexPage
 * @var $product \Model\Product\Entity
 * @var $related \Model\Product\Entity[]
 * @var $accessories \Model\Product\Entity[]
 */

$services = $product->getService();
$warranties = $product->getWarranty();
?>

<article class="bGoodItem">    
    
    <div class="bGoodItemHead mMB20 mRounded mBlackBlock clearfix">
        <div class="clearfix">
            <div class="bGoodImgBlock mRounded mFl mW940">
                <div class="bPreviewImg">
                    <? $i = 0; foreach ($product->getPhoto() as $photo): if ($i > 4) continue; ?>
                        <a class="bPreviewImg_eLink mRounded" href="#" onclick="terminal.screen.push('media', {productId: <?= $product->getId() ?>, currentIndex : <?= $i ?>});">
                            <img class="bPreviewImg_eImage" src="<?= $photo->getUrl(2) ?>" alt="<?= $page->escape($product->getName()) ?>"/>
                        </a>
                    <?  $i++; endforeach ?>
                </div>
                <div class="bGoodImgBlock_eMainImg">
                    <? if ($product->getLabel()): ?>
                        <img class="bLabels" src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $product->getLabel()->getName() ?>" />
                    <? endif ?>
                    <a href="#" onclick="terminal.screen.push('media', {productId: <?= $product->getId() ?>, currentIndex : 0});"><img width="480" src="<?= $product->getImageUrl(3) ?>" alt="<?= $page->escape($product->getName()) ?>"/></a>
                </div>
                <a class="bGoodImgBlock_eEnlarge" href="#" onclick="terminal.screen.push('media', {productId: <?= $product->getId() ?>, currentIndex : 0});"></a>
            </div>
            
            <div class="bGoodDescBlock mFr mW570 mPad15_30">
                <div class="clearfix">
                    <p class="bGoodDescBlock_eArticle mFl">Код товара:<?= $product->getArticle() ?></p>
                    <p class="bGoodDescBlock_eRating mFr"><span class="bRating"><span class="mRate_<?= round($product->getRating()) ?>"></span></span></p>
                </div>
                <h1 class="bTitle mBold"><?= $product->getName() ?></h1>
            
                <div class="bGoodDescBlock_eSubBlock clearfix">
                    <? if($product->getPriceOld()):?>
                    <div class="bGoodDescBlock_eOldPrice"><?= $page->helper->formatPrice($product->getPriceOld()) ?> <span class="bRuble">p</span></div>
                    <? endif ?>
                    <div class="bGoodDescBlock_ePrice mFl mBold"><?= $page->helper->formatPrice($product->getPrice()) ?> <span class="bRuble">p</span></div>
                    <ul class="bGoodDescBlock_eDelivery mFl">
                        <li class="mListDisk"><strong class="mBold">Есть на центральном складе</strong></li>
                        <li class="mListDisk">Можно оформить доставку</li>
                    </ul>
                </div>

                <div class="clearfix mMB80">
                    <!--  print_r($product->getState());   -->
                    <?php if ($product->getIsBuyableByShop(\App::config()->region['shop_id'])):?>
                    <a class="bGoodDescBlock_eBayBtn bButton mOrangeBtn mFl" href="#" onclick="terminal.cart.addProduct(<?= $product->getId() ?>)">В корзину</a>
                    <?php elseif ($product->getState()->getIsShop() ):?>
                    <a class="bGoodDescBlock_eBayBtn bButton mGrayBtn mFl" href="#" onclick="terminal.screen.push('other_shops', { productId: <?= $product->getId() ?> })">Где купить?</a>
                    <? endif; ?>
                    <a class="bGoodDescBlock_eCompBtn bButton mGrayBtn mFl" href="#" onclick="terminal.compare.addProduct(<?= $product->getId() ?>)">К сравнению</a>
                </div>

                <p class="bGoodDescBlock_eShortDesc"><?= $product->getTagline() ?>
                    <? if (!empty($description)): ?>
                    <a class="bGoodDescBlock_eMore" href="#">Подробнее...</a>
                    <? endif ?>
                </p>
            </div>
        </div>

        <?php if (count($accessories) || count($related)): ?>
        <div class="bGoodSubItems">
            <p class="bGoodSubItems_eHeader">
                <?php if (count($accessories)): ?>
                <a href="#" class="bGoodSubItems_eTitle active jsAccessorise">Аксессуары</a>
                <?php endif ?>
                <?php if (count($related)): ?> 
                <a href="#" class="bGoodSubItems_eTitle jsSimilar">Похожие товары</a>
            <?php endif ?>
            </p>

            <!-- accessorise -->
            <div class="bGoodSubItem clearfix">
                <div class="bSlider">
                   <!--  <a class="bSlider_eArrow mLeft" href="#"></a>
                    <a class="bSlider_eArrow mRight" href="#"></a> -->
                    <div id="accessoriseSlider" class="bSlider_eWrap clearfix">
                        <? foreach ($accessories as $iProduct): ?>
                        <div class="bGoodSubItem_eGoods mFl">
                            <a class="bGoodSubItem_eGoodsImg mFl mRounded" href="#" onclick='terminal.screen.push("product", {productId: <?= $iProduct->getId() ?>})'><img width="130" height="130" src="<?= $iProduct->getImageUrl(1) ?>"/></a>
                            <div class="bGoodSubItem_eGoodsInfo">
                                <!-- <p class="bGoodSubItem_eRating"><?= $iProduct->getRating() ?></p> -->
                                <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink" href="#" onclick='terminal.screen.push("product", {productId: <?= $iProduct->getId() ?>})'><?= $iProduct->getName() ?></a></h2>
                                <p class="bGoodSubItem_ePrice"><?= $page->helper->formatPrice($iProduct->getPrice()) ?> <span class="bRuble">p</span></p>
                                <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn" href="#" onclick='terminal.cart.addProduct(<?= $iProduct->getId() ?>)'>В корзину</a>
                            </div>
                        </div>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
            
            <!-- similar goods -->
            <div class="bGoodSubItem">
                <div class="bSlider">
                    <!-- <a class="bSlider_eArrow mLeft" href="#"></a>
                    <a class="bSlider_eArrow mRight" href="#"></a> -->
                    <div id="similarSlider" class="bSlider_eWrap mHidden clearfix">
                        <? foreach ($related as $iProduct): ?>
                        <!-- terminal.screen.push('product', {productId: <?= $iProduct->getId() ?>}) -->
                        <div class="bGoodSubItem_eGoods mFl">
                            <a class="bGoodSubItem_eGoodsImg mFl mRounded" href="#" onclick='terminal.screen.push("product", {productId: <?= $iProduct->getId() ?>})'><img width="130" height="130" src="<?= $iProduct->getImageUrl(1) ?>"/></a>
                            <div class="bGoodSubItem_eGoodsInfo">
                                <!-- <p class="bGoodSubItem_eRating"><?= $iProduct->getRating() ?></p> -->
                                <h2 class="bGoodSubItem_eTitle"><a class="bGoodSubItem_eLink" href="#" onclick='terminal.screen.push("product", {productId: <?= $iProduct->getId() ?>})'><?= $iProduct->getName() ?></a></h2>
                                <p class="bGoodSubItem_ePrice"><?= $page->helper->formatPrice($iProduct->getPrice()) ?> <span class="bRuble">p</span></p>
                                <a class="bGoodSubItem_eMore bButton mSmallOrangeBtn" href="#" onclick='terminal.cart.addProduct(<?= $iProduct->getId() ?>)'>В корзину</a>
                            </div>
                        </div>
                        <? endforeach ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif ?>
    </div>

    <div class="clearfix">
        <div class="bGoodItemSpecifications mW960 mPad15_30 mRounded mBlackBlock mFl">
            <h2 class="bGoodItemSpecifications_eTitle">Характеристики</h2>
            <? foreach ($product->getGroupedProperties() as $group): ?>
            <div class="bGoodSpecification">
                <h3 class="bGoodSpecification_eBlockName"><?= $group['group']->getName() ?></h3>
                <? foreach ($group['properties'] as $property): ?>
                    <div class="bGoodSpecification_eBlockDesc clearfix">
                        <span class="bGoodSpecification_eSpecTitle mFl"><?= $property->getName() ?></span>
                        <span class="bGoodSpecification_eSpecValue mFl"><?= $property->getStringValue() ?></span>
                        <? if ($property->getHint()): ?>
                        <div class="bQuestionIco mFl">
                            <span class="jsHint mHidden"><?= $property->getHint() ?></span>
                        </div>
                        <? endif ?>
                    </div>
                <? endforeach ?>
            </div>
            <? endforeach ?>
            <? if (!empty($description)): ?>
            <h2 class="bGoodItemSpecifications_eTitle">Описание</h2>
            <p class="bGoodItemFullDesc"><?= $product->getDescription() ?></p>
            <? endif ?>
        </div>

        <!-- models -->
        <? if((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): ?>
        <div class="bGoodItemModel mW570 mPad15_30 mRounded mBlackBlock mMB20 mFr">
            <h2 class="bGoodItemSpecifications_eTitle">Изменить параметры товара</h2>
            <? foreach ($product->getModel()->getProperty() as $property): ?>
            <div class="bGoodItemModel_eProperty clearfix">
                <div class="bGoodItemModel_ePropertyName mFl"><?= $property->getName() ?></div>
                <div class="bGoodItemModel_ePropertySelect mFl bCustomSelect clearfix">
                    <?
                        $productAttribute = $product->getPropertyById($property->getId());
                        if (!$productAttribute) break;
                    ?>
                    <div class="bCustomSelect_eElements">
                        <? foreach ($property->getOption() as $option): ?>
                        <? if ($option->getValue() == $productAttribute->getValue())continue; ?>
                        <a onclick="terminal.screen.push('product', {productId: <?= $option->getProduct()->getId() ?>})" class="bCustomSelect_eOption"><?= $option->getHumanizedName() ?></a>
                        <? endforeach ?>
                    </div>
                    <div class="bCustomSelect_eSelected bButton mGrayBtn"><?= $productAttribute->getStringValue() ?></div>
                </div>
            </div>
            <? endforeach; ?>
        </div>
        <? endif ?>
        <!-- end models -->

        <?php if (count($warranties) || count($services)): ?>
        <div class="bGoodItemF1 mW570 mPad15_30 mRounded mBlackBlock mFr">
            <h2 class="bGoodItemF1_eServiceTitle">Выбирайте услуги вместе с этим товаром</h2> 
            <!-- warranty -->
            <? foreach ($warranties as $warranty): ?>
                <div class="bGoodServiceItem clearfix">
                    <div class="bGoodServiceItem_eLogo mRounded mFl mWarranty_<?= $warranty->getId() ?>"></div>
                    <div class="bFl">
                        <p class="bGoodServiceItem_eTitle"><?= $warranty->getName() ?></p>
                        <p class="bGoodServiceItem_ePrice"><?= $page->helper->formatPrice($warranty->getPrice()) ?> <span class="bRuble">p</span></p>
                        <a class="bButton mSmallOrangeBtn mFl" href="#" onclick="terminal.cart.setWarranty(<?= $product->getId() ?>, <?= $warranty->getId() ?>)">в корзину</a>
                    </div>
                </div>
            <? endforeach ?>

            <!-- services -->
            <? foreach ($services as $service): ?>
                <div class="bGoodServiceItem clearfix">
                    <!-- <p><?= $service->getToken() ?></p> -->
                    <!-- <p><?= $service->getId() ?></p> -->
                    <a onclick="terminal.screen.push('service', {serviceId: <?= $service->getId() ?>, productId: <?= $product->getId() ?>, isBuyable: true})" class="bGoodServiceItem_eLogo mRounded mFl"></a>
                    <div class="bFl">
                        <p class="bGoodServiceItem_eTitle"><?= $service->getName() ?></p>
                        <p class="bGoodServiceItem_ePrice"><?= $page->helper->formatPrice($service->getPrice()) ?> <span class="bRuble">p</span></p>
                        <a class="bButton mSmallOrangeBtn mFl" href="#" onclick="terminal.cart.addService(<?= $product->getId() ?>, <?= $service->getId() ?>)">в корзину</a>
                        <a class="bButton mSmallGrayBtn mFl" href="#" onclick="terminal.screen.push('service', {serviceId: <?= $service->getId() ?>, productId: <?= $product->getId() ?>, isBuyable: <?= $product->getIsBuyableByShop(\App::config()->region['shop_id']) ? 'true' : 'false' ?>})">подробнее</a> <!-- isBuyable ??? -->
                    </div>
                </div>
            <? endforeach ?>
        </div>
        <?php endif ?>
    </div>

    <!-- окно подсказок -->
    <div id="bHintPopup" class="mRounded mW570 mPad15_30 mFr mHidden">
        <div class="leftCorner"></div>
        <div class="bHintPopup_eContent"></div>
    </div>
</article>