<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $useLens = true
) {
    // TODO: SITE-1822
    //$useLens = true;

    $videoHtml = '';
    $megavisor3dUrl = '';
    $swf3dUrl = '';
    $maybe3dUrl = '';
    foreach ($product->medias as $media) {
        switch ($media->provider) {
            case 'vimeo':
                $source = $media->getSourceByType('reference');
                if ($source) {
                    $width = 700;
                    $height = ceil($width / ($source->width / $source->height));
                    $videoHtml = '<iframe src="' . $helper->escape($source->url) . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
                }

                break;
            case 'youtube':
                $source = $media->getSourceByType('reference');
                if ($source) {
                    $width = 700;
                    $height = ceil($width / ($source->width / $source->height));
                    $videoHtml = '<iframe src="//www.youtube.com/embed/' . $helper->escape($source->id) . '" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen></iframe>';
                }

                break;
            case 'megavisor':
                $source = $media->getSourceByType('reference');
                if ($source) {
                    $megavisor3dUrl = 'http://media.megavisor.com/player/player.swf?uuid=' . urlencode($source->id);
                }

                break;
            case 'swf':
                $source = $media->getSourceByType('reference');
                if ($source) {
                    $swf3dUrl = $source->url;
                }

                break;
            case 'maybe3d':
                $source = $media->getSourceByType('swf');
                if ($source) {
                    $maybe3dUrl = $source->url;
                }

                break;
        }
    }

    if ($maybe3dUrl) {
        $model3dUrl = $maybe3dUrl;
    } else if ($megavisor3dUrl) {
        $model3dUrl = $megavisor3dUrl;
    } else if ($swf3dUrl) {
        $model3dUrl = $swf3dUrl;
    } else {
        $model3dUrl = '';
    }
?>

<? if ($model3dUrl): ?>
    <div id="maybe3dModelPopup" class="popup js-product-3d-swf-popup" data-url="<?= $helper->escape($model3dUrl); ?>">
        <i class="close" title="Закрыть">Закрыть</i>
        <div class="js-product-3d-swf-popup-container" style="position: relative;">
            <div id="js-product-3d-swf-popup-placeholder">
                <a href="http://www.adobe.com/go/getflashplayer">
                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                </a>
            </div>
        </div>
    </div>
<? endif ?>

<? if ($product->json3d) : ?>
    <div class="popup js-product-3d-img-popup" data-value="<?= $helper->json($product->json3d); ?>" data-host="<?= $helper->json(['http://' . App::request()->getHost()]) ?>">
        <i class="close" title="Закрыть">Закрыть</i>
    </div>
<? endif ?>

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

    <div class="bProductDescImgBig">
        <img itemprop="image" class="bProductDescImgBig__eImg bZoomedImg"
             src="<?= $product->getImageUrl(3) ?>"
             <? if ($useLens): ?>
                data-zoom-image="<?= $product->getImageUrl(5) ?>"
             <? endif ?>
             data-zoom-disable="<?= $useLens ? false : true ?>"
             alt="<?= $helper->escape($product->getName()) ?>"
        />

        <? if (!$product->isAvailable() && (!$product->getLabel() || mb_strtolower($product->getLabel()->getName()) !== 'подари жизнь')): ?>
            <div class="bProductDescImgBig_none">Нет в наличии</div>
        <? endif ?>
    </div><!--/product big image section -->

    <div class="bPhotoAction clearfix">
        <ul class="bPhotoViewer">
            <? if ($videoHtml): ?>
                <li class="bPhotoViewer__eItem mVideo js-product-video">
                    <a class="bPhotoLink" href="#"></a>
                    <div class="blackPopup blackPopupVideo js-product-video-container">
                        <div class="close"></div>
                        <div class="productVideo_iframe js-product-video-iframeContainer"><?= $videoHtml ?></div>
                    </div>
                </li>
            <? endif ?>

            <? if ($model3dUrl): ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-swf-opener">
                    <a class="bPhotoLink" href=""></a>
                </li>
            <? endif ?>

            <? if ($product->json3d): ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 js-product-3d-img-opener">
                    <a class="bPhotoLink" href=""></a>
                </li>
            <? endif ?>
        </ul><!--/view product section -->

        <? if (count($product->getPhoto()) > 1): ?>
                <div class="prod-photoslider js-photoslider">
                <div class="prod-photoslider__wrap">
                    <ul id="productImgGallery" class="prod-photoslider__gal clearfix js-photoslider-gal">
                        <? $i = 0; foreach ($product->getPhoto() as $photo):
                            $zoomDisable = ($photo->getHeight() > 750 || $photo->getWidth() > 750) ? false : true; ?>
                            <li class="prod-photoslider__gal__i js-photoslider-gal-i">
                                <a class="prod-photoslider__gal__link jsPhotoGalleryLink<? if (0 == $i): ?> prod-photoslider__gal__link--active<? endif ?>" data-zoom-image="<?= $photo->getUrl(5) ?>" data-image="<?= $photo->getUrl(3) ?>" href="#" data-zoom-disable="<?= $zoomDisable ?>">
                                    <img class="prod-photoslider__gal__img" src="<?= $photo->getUrl(0) ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                                </a>
                            </li>
                        <? $i++; endforeach ?>
                    </ul>
                </div>

                <div class="prod-photoslider__btn prod-photoslider__btn--prev js-photoslider-btn-prev"><span class="prod-photoslider__btn__arw"></span></div>
                <div class="prod-photoslider__btn prod-photoslider__btn--next js-photoslider-btn-next"><span class="prod-photoslider__btn__arw"></span></div>
            </div><!--/slider mini product images -->
        <? endif ?>
    </div>
</div><!--/product images section -->

<? };