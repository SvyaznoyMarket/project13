<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
) {
    $videoHtml = '';
    $maybe3dHtml5Source = null;

    $megavisor3dUrl = '';
    $swf3dUrl = '';
    $maybe3dSwfUrl = '';
    foreach ($product->getMedias() as $media) {
        switch ($media->provider) {
            case 'vimeo':
                $source = $media->getSource('reference');
                if ($source) {
                    $width = 700;
                    $height = ceil($width / ($source->width / $source->height));
                    $videoHtml = '<iframe src="' . $helper->escape($source->url) . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                }

                break;
            case 'youtube':
                $source = $media->getSource('reference');
                if ($source) {
                    $width = 700;
                    $height = ceil($width / ($source->width / $source->height));
                    $videoHtml = '<iframe src="//www.youtube.com/embed/' . $helper->escape($source->id) . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen></iframe>';
                }

                break;
            case 'megavisor':
                $source = $media->getSource('reference');
                if ($source) {
                    $megavisor3dUrl = 'http://media.megavisor.com/player/player.swf?uuid=' . urlencode($source->id);
                }

                break;
            case 'swf':
                $source = $media->getSource('reference');
                if ($source) {
                    $swf3dUrl = $source->url;
                }

                break;
            case 'maybe3d':
                // Временно отключаем maybe3d html5 модели из-за проблем, описанных в SITE-3783
                /*if ($source = $media->getSource('html5')) {
                    $maybe3dHtml5Source = $source;
                } else*/ if ($source = $media->getSource('swf')) {
                $maybe3dSwfUrl = $source->url;
            }

                break;
        }
    }

    if ($maybe3dSwfUrl) {
        $model3dSwfUrl = $maybe3dSwfUrl;
    } else if ($megavisor3dUrl) {
        $model3dSwfUrl = $megavisor3dUrl;
    } else if ($swf3dUrl) {
        $model3dSwfUrl = $swf3dUrl;
    } else {
        $model3dSwfUrl = '';
    }

    /** @var \Model\Media[] $images */
    $images = array_merge($product->getMedias('image', 'main'), $product->getMedias('image', 'additional'));
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
                <img class="bProductDescImgBig__eImg bZoomedImg mLoader js-photo-zoomedImg"
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

                <? if ($maybe3dHtml5Source): ?>
                    <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-html5-opener">
                        <a class="bPhotoLink" href=""></a>
                        <div id="maybe3dModelPopup" class="popup js-product-3d-html5-popup" data-url="<?= $helper->escape($maybe3dHtml5Source->url); ?>" data-id="<?= $helper->escape($maybe3dHtml5Source->id); ?>">
                            <i class="close jsPopupCloser" title="Закрыть">Закрыть</i>
                            <div class="js-product-3d-html5-popup-container" style="position: relative;">
                                <div id="js-product-3d-html5-popup-model" class="model"></div>
                            </div>
                        </div>
                    </li>
                <? elseif ($model3dSwfUrl): ?>
                    <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-swf-opener">
                        <a class="bPhotoLink" href=""></a>
                        <div id="maybe3dModelPopup" class="popup js-product-3d-swf-popup" data-url="<?= $helper->escape($model3dSwfUrl); ?>">
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
                        <div class="popup js-product-3d-img-popup" data-value="<?= $helper->json($product->json3d); ?>">
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
