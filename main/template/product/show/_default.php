<?php
/**
 * @var $page        \View\Layout
 * @var $user        \Session\User
 * @var $product     \Model\Product\Entity
 * @var $creditData  array
 */
?>

<?
$hasLowerPriceNotification =
    \App::config()->product['lowerPriceNotification']
    && $product->getMainCategory() && $product->getMainCategory()->getPriceChangeTriggerEnabled();
?>

<div class="goodsphoto">
    <? if ($productVideo && $productVideo->getContent()): ?><a class="goodsphoto_eVideoShield" href="#"></a><? endif ?>

    <a href="<?= $product->getImageUrl(4) ?>" class="viewme" ref="image" onclick="return false">
        <? if ($product->getLabel()): ?>
            <img class="bLabels" src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $page->escape($product->getLabel()->getName()) ?>" />
        <? endif ?>
        <img class="mainImg" src="<?= $product->getImageUrl(3) ?>" alt="<?= $page->escape($product->getName()) ?>" title="<?= $page->escape($product->getName()) ?>" width="500" height="500" />
    </a>


    <!-- Photo video -->
    <? if (count($photo3dList) > 0 || count($photoList) > 0): ?>
        <div class="fl width500">
            <h2>Фото товара:</h2>
            <div class="font11 gray pb10">Всего фотографий <?= count($photoList) ?></div>
            <ul class="previewlist">
                <? foreach ($photoList as $photo): ?>
                    <li class="viewstock" ref="photo<?= $photo->getId() ?>">
                        <a href="<?= $photo->getUrl(4) ?>" class="viewme" ref="image">
                            <img src="<?= $photo->getUrl(2) ?>" alt="<?= $page->escape($product->getName()) ?>" title="<?= $page->escape($product->getName()) ?>" width="48" height="48" />
                        </a>
                    </li>
                <? endforeach ?>
                <? if (count($photo3dList) > 0 || $model3dExternalUrl || $model3dImg): ?>
                    <li><a href="#" class="axonometric viewme <? if ($model3dExternalUrl): ?>maybe3d<? endif ?>  <? if ($model3dImg): ?>3dimg<? endif ?>" ref="360" title="Объемное изображение">Объемное изображение</a></li>
                <? endif ?>
            </ul>
        </div>
    <? endif ?>
    <!-- /Photo video -->
</div>

<div style="display:none;" id="stock">
    <!-- list of images 500*500 for preview -->
    <? foreach ($photoList as $photo): ?>
        <img src="<?= $photo->getUrl(3) ?>" alt="" data-url="<?= $photo->getUrl(4) ?>" ref="photo<?= $photo->getId() ?>" width="500" height="500" title="" />
    <? endforeach ?>
</div>

<!-- Goods info -->
<div class="goodsinfo bGood">
    <div class="bGood__eArticle clearfix">
        <span>Артикул: <span itemprop="productID"><?= $product->getArticle() ?></span></span>
    </div>

    <? if (\App::config()->product['reviewEnabled']): ?>
        <div class="reviewSection reviewSection100 clearfix">

            <div class="reviewSection__link">
                <div class="reviewSection__star reviewSection100__star">
                    <? $avgStarScore = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'] ?>
                    <?= empty($avgStarScore) ? '' : $page->render('product/_starsFive', ['score' => $avgStarScore]) ?>
                </div>

                <? if (!empty($avgStarScore)) { ?>
                    <span class="border" onclick="scrollToId('reviewsSectionHeader')"><?= $reviewsData['num_reviews'] ?> <?= $page->helper->numberChoice($reviewsData['num_reviews'], array('отзыв', 'отзыва', 'отзывов')) ?></span>
                <? } else { ?>
                    <span>Отзывов нет</span>
                <? } ?>
                <span class="reviewSection__link__write newReviewPopupLink" data-pid="productid">Оставить отзыв</span>
                <div class="hf" id="reviewsProductName"><?= $product->getName() ?></div>
            </div>
            <div style="position:fixed; top:40px; left:50%; margin-left:-442px; z-index:1002; display:none; width:700px; height:480px" class="reviewPopup popup clearfix">
                <a class="close" href="#">Закрыть</a>
                <iframe id="rframe" frameborder="0" scrolling="auto" height="480" width="700"></iframe>
            </div>
        </div>
    <? endif ?>

    <div class="font14 pb15" itemprop="description"><?= $product->getTagline() ?></div>
    <div class="clear"></div>

    <? if($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany()): ?>
        <div style="text-decoration: line-through; font: normal 18px verdana; letter-spacing: -0.05em; color: #6a6a6a;"><span class="price"><?= $page->helper->formatPrice($product->getPriceOld()) ?></span> <span class="rubl">p</span></div>
    <? elseif($showAveragePrice): ?>
        <div class="mOurGray">
            Средняя цена в магазинах города*<br/><div class='mOurGray mIco'><span class="price"><?= $page->helper->formatPrice($product->getPriceAverage()) ?></span> <span class="rubl">p</span> &nbsp;</div>
        </div>
        <div class="clear"></div>
        <div class="clear mOur pt10 <? if ($product->hasSaleLabel()) echo 'red'; ?>">Наша цена</div>
    <? endif ?>

    <div class="fl pb15" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
        <div class="pb10 <? if ($product->hasSaleLabel()) echo 'red'; ?>"><strong class="font34"><span class="price" itemprop="price"><?= $page->helper->formatPrice($product->getPrice()) ?></span> <meta itemprop="priceCurrency" content="RUB"><span class="rubl">p</span></strong></div>

        <? if ($hasLowerPriceNotification): ?>
        <?
            $lowerPrice =
                ($product->getMainCategory() && $product->getMainCategory()->getPriceChangePercentTrigger())
                ? round($product->getPrice() * $product->getMainCategory()->getPriceChangePercentTrigger())
                : 0;
        ?>
        <a href="#" class="bLowPriceNotifer jsLowPriceNotifer">Сообщить о снижении цены</a>
        <div class="bLowPriceNotiferPopup popup">
            <i class="close"></i>
            <h2 class="bLowPriceNotiferPopup__eTitle">
                Вы получите письмо,<br/>когда цена станет ниже
                <? if ($lowerPrice && ($lowerPrice < $product->getPrice())): ?>
                    <strong class="price"><?= $page->helper->formatPrice($lowerPrice) ?></strong> <span class="rubl">p</span>
                <? endif ?>
            </h2>
            <input class="bLowPriceNotiferPopup__eInputEmail" placeholder="Ваш email" value="<?= $user->getEntity() ? $user->getEntity()->getEmail() : '' ?>" />
            <p class="bLowPriceNotiferPopup__eError red"></p>
            <a href="#" class="bLowPriceNotiferPopup__eSubmitEmail button bigbuttonlink" data-url="<?= $page->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>">Сохранить</a>
        </div>
        <? endif ?>

        <? if ($product->getIsBuyable()): ?>
            <link itemprop="availability" href="http://schema.org/InStock" />
            <div class="pb5"><strong class="orange">Есть в наличии</strong></div>
        <? elseif (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
            <link itemprop="availability" href="http://schema.org/InStoreOnly" />
        <? else: ?>
            <link itemprop="availability" href="http://schema.org/OutOfStock" />
        <? endif ?>
    </div>

    <div class="fr ar pb15">

        <div class="goodsbarbig mSmallBtns" ref="<?= $product->getToken() ?>" data-value='<?= $json ?>'>
            <? if ($product->getIsBuyable()): ?>
                <div class='bCountSet'>
                    <? if (!$user->getCart()->hasProduct($product->getId())): ?>
                        <a class='bCountSet__eP' href="#">+</a><a class='bCountSet__eM' href="#">-</a>
                    <? else: ?>
                        <a class='bCountSet__eP disabled' href="#">&nbsp;</a><a class='bCountSet__eM disabled' href="#">&nbsp;</a>
                    <? endif ?>
                    <span><?= $user->getCart()->hasProduct($product->getId()) ? $user->getCart()->getQuantityByProduct($product->getId()) : 1 ?> шт.</span>
                </div>
            <?php endif ?>
            <?= $page->render('cart/_button', ['product' => $product, 'disabled' => !$product->getIsBuyable()]) ?>
        </div>
        <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
            <div class="notBuying font12">
                <div class="corner"><div></div></div>
                Только в магазинах
            </div>
        <? endif ?>
        <? if ($product->getIsBuyable()): ?>
            <div class="pb5"><strong>
                    <a href=""
                       data-model='<?= $json ?>'
                       link-output='<?= $page->url('order.1click', ['product' => $product->getToken()]) ?>'
                       link-input='<?= $page->url('product.delivery_1click') ?>'
                       class="red underline order1click-link-new">Купить быстро в 1 клик</a>
                </strong></div>
        <? endif ?>
    </div>
    <? if (!$product->getIsBuyable() && $product->getState()->getIsShop()): ?>
        <div class="fr ar pb15">

            <div class="vitrin" id="availableShops" data-shops='<?= $jsonAvailableShops ?>'>
                <div class="line pb15"></div>
                <p class="font18 orange">Этот товар вы можете купить только в магазин<?= (count($shopsWithQuantity) == 1) ? 'е' : 'ах' ?></p>
                <ul id="listAvalShop">
                    <? $i = 0; foreach ($shopsWithQuantity as $shopWithQuantity): $i++?>
                        <li<?= $i > 3 ? ' class="hidden"' : ''?>>
                            <a class="fr dashedLink shopLookAtMap" href="#">Посмотреть на карте</a>
                            <?= '<a class="avalShopAddr" href="'.$page->url('shop.show', ['shopToken' => $shopWithQuantity['shop']->getToken(), 'regionToken' => $user->getRegion()->getToken()]) . '" class="underline">' . $shopWithQuantity['shop']->getName() . '</a>' ?>
                            <strong class="font12 orange db pt10"><?= ($shopWithQuantity['quantity'] > 5 ? 'есть в наличии' : ($shopWithQuantity['quantity'] > 0 ? 'осталось мало' : ($shopWithQuantity['quantityShowroom'] > 0 ? 'есть только на витрине' : ''))) ?></strong>
                        </li>
                    <? endforeach ?>
                </ul>
                <?php if (count($shopsWithQuantity) > 3): ?>
                    <a id="slideAvalShop" class="orange strong dashedLink font18" href="#">Еще <?= count($shopsWithQuantity) - 3 ?> <?= $page->helper->numberChoice(count($shopsWithQuantity) - 3, ['магазин', 'магазина', 'магазинов']) ?></a>
                <?php endif ?>
            </div>
        </div>
    <? endif ?>
    <div class="line pb15"></div>


    <? if ($product->getIsBuyable()): ?>

        <? if ($creditData['creditIsAllowed'] && !$user->getRegion()->getHasTransportCompany()) : ?>
            <div class="creditbox">
                <div class="creditboxinner">
                    <div class="creditLeft">от <span class="font24"><b class="price"></b> <b class="rubl">p</b></span> в кредит</div>
                    <div class="fr pt5"><label class="bigcheck " for="creditinput"><b></b>Беру в кредит
                            <input id="creditinput" type="checkbox" name="creditinput" autocomplete="off"/></label>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        <? endif; ?>

        <? if ($creditData['creditIsAllowed']) : ?>
            <input data-model="<?= $page->escape($creditData['creditData']) ?>" id="dc_buy_on_credit_<?= $product->getArticle(); ?>" name="dc_buy_on_credit" type="hidden" />
        <? endif; ?>

    <? elseif ($user->getRegion()->getHasTransportCompany()): ?>
        <? if (\App::config()->product['globalListEnabled'] && (bool)$product->getNearestCity()): ?>
            <?= $page->render('product/_nearestCity', ['product' => $product]) ?>
        <? else: ?>
            <p>Этот товар мы доставляем только в регионах нашего присутствия</p>
        <? endif ?>
    <?php endif ?>

    <? if ($product->getIsBuyable()): ?>
        <div class="bDeliver2 delivery-info" id="product-id-<?= $product->getId() ?>" data-shoplink="<?= $page->url('product.stock', ['productPath' => $product->getPath()]) ?>" data-calclink="<?= $page->url('product.delivery') ?>">
            <h4>Как получить заказ?</h4>
            <ul>
                <li>
                    <h5>Идет расчет условий доставки...</h5>
                </li>
            </ul>
        </div>
    <?php endif ?>

    <? if (\App::config()->adFox['enabled']): ?>
        <div style="margin-bottom: 20px;">
            <div class="adfoxWrapper" id="<?= $adfox_id_by_label ?>"></div>
        </div>
    <? endif ?>

    <? if ($product->getIsBuyable()): ?>
        <?= $page->render('service/_listByProduct', ['product' => $product]) ?>
        <?= $page->render('warranty/_listByProduct', ['product' => $product]) ?>
    <? endif ?>

</div>
<!-- /Goods info -->
