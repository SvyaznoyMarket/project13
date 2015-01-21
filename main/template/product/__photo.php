<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    array $productVideos,
    $useLens = true
) {
    /** @var  $productVideo \Model\Product\Video\Entity|null */
    $productVideo = reset($productVideos);

    // TODO: SITE-1822
    //$useLens = true;

    /** @var string $model3dExternalUrl */
    $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : null;
    /** @var string $model3dImg */
    $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : null;
    /** @var array $photo3dList */
    $photo3dList = [];
    /** @var array $p3d_res_small */
    $p3d_res_small = [];
    /** @var array $p3d_res_big */
    $p3d_res_big = [];

    if (!$model3dExternalUrl && !$model3dImg) {
        $photo3dList = $product->getPhoto3d();
        foreach ($photo3dList as $photo3d) {
            $p3d_res_small[] = $photo3d->getUrl(0);
            $p3d_res_big[] = $photo3d->getUrl(1);
        }
    } elseif ($model3dExternalUrl) {
        $model3dName = preg_replace('/\.swf|\.swf$/iu', '', basename($model3dExternalUrl));
        if (!strlen($model3dName)) $model3dExternalUrl = false;
    }

    $maybe3dData = $model3dExternalUrl
        ? [
            'init' => [
                'swf'       => $model3dExternalUrl,
                'container' => 'maybe3dModel',
                'width'     => '700px',
                'height'    => '500px',
                'version'   => '10.0.0',
                'install'   => 'js/vendor/expressInstall.swf',
            ],
            'params' => [
                'menu'              => 'false',
                'scale'             => 'noScale',
                'allowFullscreen'   => 'true',
                'allowScriptAccess' => 'always',
                'wmode'             => 'direct',
            ],
            'attributes' => [
                'id' => $model3dName,
            ],
            'flashvars' => [
                'language' => "auto",
            ]

        ]
        : null
    ;
?>

<? if ((bool)$maybe3dData): ?>
    <div id="maybe3dModelPopup" class="popup" data-value="<?= $helper->json($maybe3dData); ?>">
        <i class="close" title="Закрыть">Закрыть</i>
        <div id="maybe3dModelPopup_inner" style="position: relative;">
            <div id="maybe3dModel">
                <a href="http://www.adobe.com/go/getflashplayer">
                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                </a>
            </div>
        </div>
    </div>
<? endif ?>

    <? if ((bool)true): ?>
        <div id="vFittingModelPopup" class="popup" data-value="<?= $helper->json($maybe3dData); ?>">
            <i class="close" title="Закрыть">Закрыть</i>
            <div id="vFittingModelPopup_inner" style="position: relative;">
                <div id="vFittingModel">
                    <a href="http://www.adobe.com/go/getflashplayer">
                        <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                    </a>
                </div>
            </div>
        </div>
    <? endif ?>

<? if ($model3dImg) : ?>
    <div id="3dModelImg" class="popup" data-value="<?= $helper->json($model3dImg); ?>" data-host="<?= $helper->json(['http://'.App::request()->getHost()]) ?>">
        <i class="close" title="Закрыть">Закрыть</i>
    </div>
<? endif ?>

<script type="text/javascript">
<? if ($model3dExternalUrl) : ?>
    product_3d_url = <?= json_encode($model3dExternalUrl) ?>;
<? elseif (count($photo3dList) > 0) : ?>
    product_3d_small = <?= json_encode($p3d_res_small) ?>;
    product_3d_big = <?= json_encode($p3d_res_big) ?>;
<? endif ?>
</script>


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
            <? if ($productVideo && $productVideo->getContent()): ?>
                <li class="bPhotoViewer__eItem mVideo">
                    <a class="bPhotoLink" href="#"></a>
                    <div id="productVideo" class="blackPopup blackPopupVideo">
                        <div class="close"></div>
                        <div class="productVideo_iframe"><?= $productVideo->getContent() ?></div>
                    </div>
                </li>
            <? endif ?>
            <? if ($model3dExternalUrl || $model3dImg):  ?>
                <?
                if ($model3dExternalUrl) {
                    $class3D = 'maybe3d';
                } else if ($model3dImg) {
                    $class3D = '3dimg';
                } else {
                    $class3D = 'our3d';
                }
                ?>
                <li class="bPhotoActionOtherAction__eGrad360 bPhotoViewer__eItem mGrad360 <?= $class3D ?>">
                    <a class="bPhotoLink" href=""></a>
                </li>
            <? endif ?>
            <? if ($productVideo && $productVideo->getPandra()): ?>
                <li class="bPhotoActionOtherAction bPhotoViewer__eItem vFitting">
                    <a class="bPhotoLink" href="#"></a>
                </li>
            <? endif  ?>
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