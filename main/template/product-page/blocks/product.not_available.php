<?php
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $videoHtml, $properties3D, $reviewsData, $buySender2, $favoriteProductsByUi, $trustfactors
){

    ?>

    <div class="product-card product-card--2col product-card--not clearfix">

        <!-- блок с фото -->
        <?= $helper->render('product-page/blocks/photo', ['product' => $product, 'videoHtml' => $videoHtml, 'properties3D' => $properties3D ]) ?>
        <!--/ блок с фото -->

        <div class="product-card__c">

            <!-- похожие товары -->
            <div class="product-section product-section--inn goods-slider--4items">
                <? if (\App::config()->product['pullRecommendation']): ?>
                    <?= $helper->render('product-page/blocks/slider', [
                        'type'     => 'item_page.not_in_stock',
                        'title'    => 'Похожие товары',
                        'products' => [],
                        'limit'    => \App::config()->product['itemsInSlider'],
                        'page'     => 1,
                        'url'      => $helper->url(
                            'product.recommended',
                            ['productId' => $product->model && $product->model->getMainProduct() ? $product->model->getMainProduct()->getId() : $product->getId()]
                        ),
                        'sender'   => [
                            'name'     => 'rich',
                            'position' => 'ProductSimilarNotAvailable',
                        ],
                        'sender2' => $buySender2,
                    ]) ?>
                <? endif ?>
            </div>
            <!--/ похожие товары -->

            <!-- сравнить, добавить в виш лист -->
            <ul class="product-card-tools">
                <li class="product-card-tools__i product-card-tools__i--compare js-compareProduct"
                    data-bind="compareButtonBinding: compare"
                    data-id="<?= $product->getId() ?>"
                    data-type-id="<?= $product->getType() ? $product->getType()->getId() : null ?>">
                    <a id="<?= 'compareButton-' . $product->getId() ?>"
                       href="<?= \App::router()->generateUrl('compare.add', ['productId' => $product->getId(), 'location' => 'product']) ?>"
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

            <? if (false) : ?>
                <div class="buy-online">
                    <a class="btn-type btn-type--olive js-orderButton jsBuyButton" href="#">Сообщить о наличии</a>
                </div>
            <? endif ?>

            <?= $helper->render('product-page/blocks/variants', ['product' => $product, 'trustfactors' => $trustfactors]) ?>

            <?= $helper->render('product-page/blocks/reviews.short', ['reviewsData' => $reviewsData]) ?>

            <? if ($product->getTagline()) : ?>
                <!-- краткое описание товара -->
                <p class="product-card-desc"><?= $product->getTagline() ?></p>
            <? endif ?>

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
                <script type="text/javascript">var addthis_config = {"data_track_addressbar":false, ui_language: "ru"};</script>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-51b040940ada4cd1&domready=1"></script>
                <!-- AddThis Button END -->
            </div>

            <div class="js-showTopBar"></div>
        </div>
    </div>

<? }; return $f;