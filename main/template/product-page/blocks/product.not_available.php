<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $videoHtml, $properties3D, $reviewsData, $creditData, $isKit, $buySender, $buySender2, $request
){

    ?>

    <div class="product-card product-card--2col product-card--not clearfix">

        <!-- блок с фото -->
        <?= $helper->render('product-page/blocks/photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D ]) ?>
        <!--/ блок с фото -->

        <div class="product-card__c">
            
           

            <!-- сравнить, добавить в виш лист -->
            <ul class="product-card-tools">
                <li class="product-card-tools__i product-card-tools__i--compare js-compareProduct"
                    data-bind="compareButtonBinding: compare"
                    data-id="<?= $product->getId() ?>"
                    data-type-id="<?= $product->getType() ? $product->getType()->getId() : null ?>">
                    <a id="<?= 'compareButton-' . $product->getId() ?>"
                       href="<?= \App::router()->generate('compare.add', ['productId' => $product->getId(), 'location' => 'product']) ?>"
                       class="product-card-tools__lk jsCompareLink"
                       data-is-slot="<?= (bool)$product->getSlotPartnerOffer() ?>"
                       data-is-only-from-partner="<?= $product->isOnlyFromPartner() ?>"
                        >
                        <i class="product-card-tools__icon i-tools-icon i-tools-icon--product-compare"></i>
                        <span class="product-card-tools__tx">Сравнить</span>
                    </a>
                </li>

                <li class="product-card-tools__i product-card-tools__i--wish">
                    <?= $helper->render('product/__favoriteButton', ['product' => $product, 'favoriteProduct' => isset($favoriteProductsByUi[$product->getUi()]) ? $favoriteProductsByUi[$product->getUi()] : null]) ?>
                </li>
            </ul>
            <!--/ сравнить, добавить в виш лист -->

             <?//= $helper->render('product-page/blocks/variants', ['product' => $product]) ?>
            <div class="product-card-filter">
                <span class="product-card-filter__tl">Размеры наматрасника</span>

                <div class="product-card-filter__box">
                    <div class="filter-btn-box filter-btn-box--bordered" onclick="$(this).toggleClass('filter-btn-box--open')">

                        <div class="filter-btn-box__toggle">
                            <span class="filter-btn-box__tx">120 х 200 см</span>
                            <i class="filter-btn-box__corner"></i>
                        </div>

                        <div class="filter-btn-box__dd" onmouseleave="$(this).parent().removeClass('filter-btn-box--open')">
                            <div class="filter-btn-box__inn">
                                <ul class="filter-btn-box-lst">
                                    <li class="filter-btn-box-lst__i"><a href="/product/furniture/dargez-steganiy-namatrasnik-90-h-200-sm-dargez-ideal-gold-2040502003947-120-h-200-sm" class="filter-btn-box-lst__lk">120 х 200 см</a></li>
                                    <li class="filter-btn-box-lst__i"><a href="/product/furniture/dargez-steganiy-namatrasnik-90-h-200-sm-dargez-ideal-gold-2040502003961-160-h-200-sm" class="filter-btn-box-lst__lk">160 х 200 см</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                
            </div>

            <div class="buy-online">
                <a class="btn-type btn-type--olive js-orderButton jsBuyButton" href="#">Сообщить о наличии</a>
            </div>

            <?= $helper->render('product-page/blocks/reviews.short', ['reviewsData' => $reviewsData]) ?>

            

            <?= $helper->render('product-page/blocks/variants', ['product' => $product]) ?>

            <!-- краткое описание товара -->
            <p class="product-card-desc"><?= $product->getAnnounce() ?></p>

            <dl class="product-card-prop">
                <? foreach ($product->getMainProperties() as $property) : ?>
                    <dt class="product-card-prop__i product-card-prop__i--name"><?= $property->getName() ?></dt>
                    <dd class="product-card-prop__i product-card-prop__i--val"><?= $property->getStringValue() ?></dd>
                <? endforeach ?>
            </dl>
            <!--/ краткое описание товара -->

            <div class="product-card-sharing-list">
                <!-- AddThis Button BEGIN -->
                <div class="addthis_toolbox addthis_default_style mt15 ">
                    <a class="addthis_button_facebook"></a>
                    <a class="addthis_button_twitter"></a>
                    <a class="addthis_button_vk"></a>
                    <a class="addthis_button_compact"></a>
                    <a class="addthis_counter addthis_bubble_style"></a>
                </div>
                <script type="text/javascript">var addthis_config = {"data_track_addressbar":true, ui_language: "ru"};</script>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1&domready=1"></script>
                <!-- AddThis Button END -->
            </div>

            <div class="js-showTopBar"></div>
        </div>
    </div>

<? }; return $f;