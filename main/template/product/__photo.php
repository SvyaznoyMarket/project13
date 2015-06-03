<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $useLens = true,
    $videoHtml,
    $properties3D
) {

    $maybe3dHtml5Source = null;

?>

<div class="bProductDescImg">
    <? if ($product->getLabel()): ?>
        <div class="bProductDescSticker mLeft">
            <img src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getLabel()->getName()) ?>" />
        </div>
    <? endif ?>

    <? if ($product->getBrand() && $product->getBrand()->getImage()) : ?>
        <div class="bProductDescSticker mRight">
            <img src="<?= $product->getBrand()->getImage() ?>" alt="<?= $helper->escape($product->getBrand()->getName()) ?>" />
        </div>
    <? endif ?>

    <? if ($images): ?>
        <?
        $sourceStandard = $product->isGifteryCertificate() ? $images[0]->getSource('product_1500') : $images[0]->getSource('product_500');
        $sourceBig = $images[0]->getSource('product_1500');
        $sourceOriginal = $images[0]->getSource('original');
        ?>

        <div class="bProductDescImgBig js-product-bigImg">
            <img itemprop="image" class="bProductDescImgBig__eImg bZoomedImg mLoader js-photo-zoomedImg"
                src="<?= $sourceStandard ? $helper->escape($sourceStandard->url) : '' ?>"
                <? if ($sourceBig && $sourceOriginal && ($sourceOriginal->height > 750 || $sourceOriginal->width > 750)): ?>data-zoom-image="<?= $helper->escape($sourceBig->url) ?>"<? endif ?>
                alt="<?= $helper->escape($product->getName()) ?>"
                <? if ($product->getSlotPartnerOffer()): ?>data-is-slot="true"<? endif ?>
            />

            <? if (!$product->isAvailable() && (!$product->getLabel() || mb_strtolower($product->getLabel()->getName()) !== 'подари жизнь')): ?>
                <div class="bProductDescImgBig_none">Нет в наличии</div>
            <? endif ?>
        </div>
    <? endif ?>

    <div class="bPhotoAction clearfix">
        <ul class="bPhotoViewer">
            <? if ($videoHtml): ?>
                <li class="bPhotoViewer__eItem mVideo js-product-video">
                    <a class="bPhotoLink" href="#"></a>
                    <div class="blackPopup blackPopupVideo js-product-video-container">
                        <div class="close jsPopupCloser"></div>
                        <div class="productVideo_iframe js-product-video-iframeContainer"><?= $videoHtml ?></div>
                    </div>
                </li>
            <? endif ?>

            <? if ($properties3D['type'] == 'html5'): ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-html5-opener">
                    <a class="bPhotoLink" href=""></a>
                    <div id="maybe3dModelPopup" class="popup js-product-3d-html5-popup" data-url="<?= $helper->escape($properties3D['url']); ?>" data-id="<?= $helper->escape($properties3D['id']); ?>">
                        <i class="close jsPopupCloser" title="Закрыть">Закрыть</i>
                        <div class="js-product-3d-html5-popup-container" style="position: relative;">
                            <div id="js-product-3d-html5-popup-model" class="model"></div>
                        </div>
                    </div>
                </li>
            <? elseif ($properties3D['type'] == 'swf'): ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-swf-opener">
                    <a class="bPhotoLink" href=""></a>
                    <div id="maybe3dModelPopup" class="popup js-product-3d-swf-popup" data-url="<?= $properties3D['url']; ?>">
                        <i class="close jsPopupCloser" title="Закрыть">Закрыть</i>
                        <div class="js-product-3d-swf-popup-container" style="position: relative;">
                            <div id="js-product-3d-swf-popup-model">
                                <a href="http://www.adobe.com/go/getflashplayer">
                                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            <? elseif ($product->json3d): ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-img-opener">
                    <a class="bPhotoLink" href=""></a>
                    <div class="popup js-product-3d-img-popup" data-value="<?= $helper->json($product->json3d); ?>" data-host="<?= $helper->json(['http://' . App::request()->getHost()]) ?>">
                        <i class="close jsPopupCloser" title="Закрыть">Закрыть</i>
                    </div>
                </li>
            <? endif ?>
        </ul><!--/view product section -->

        <? if (count($images) > 1): ?>
            <div class="prod-photoslider js-photoslider">
                <div class="prod-photoslider__wrap">
                    <ul id="productImgGallery" class="prod-photoslider__gal clearfix js-photoslider-gal">
                        <? foreach ($images as $i => $image): ?>
                            <?
                            $sourceBig = $image->getSource('product_1500');
                            $sourceStandard = $product->isGifteryCertificate() ? $image->getSource('product_1500') : $image->getSource('product_500');
                            $sourceOriginal = $image->getSource('original');
                            $sourceSmall = $image->getSource('product_60');
                            ?>
                            <? if ($sourceStandard && $sourceStandard->url): ?>
                                <li class="prod-photoslider__gal__i js-photoslider-gal-i">
                                    <a class="prod-photoslider__gal__link jsPhotoGalleryLink<? if (0 == $i): ?> prod-photoslider__gal__link--active<? endif ?>" <? if ($sourceBig && $sourceOriginal && ($sourceOriginal->height > 750 || $sourceOriginal->width > 750)): ?>data-zoom-image="<?= $helper->escape($sourceBig->url) ?>"<? endif ?> data-image="<?= $helper->escape($sourceStandard->url) ?>" href="#">
                                        <img class="prod-photoslider__gal__img" src="<?= $sourceSmall ? $helper->escape($sourceSmall->url) : '' ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                                    </a>
                                </li>
                            <? endif ?>
                        <? endforeach ?>
                    </ul>
                </div>

                <div class="prod-photoslider__btn prod-photoslider__btn--prev js-photoslider-btn-prev"><span class="prod-photoslider__btn__arw"></span></div>
                <div class="prod-photoslider__btn prod-photoslider__btn--next js-photoslider-btn-next"><span class="prod-photoslider__btn__arw"></span></div>
            </div><!--/slider mini product images -->
        <? endif ?>
    </div>
</div><!--/product images section -->

<? };