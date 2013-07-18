<?php

return function(
    \Model\Product\Entity $product,
    array $productVideos,
    \Helper\TemplateHelper $helper
) {
    /** @var  $productVideo \Model\Product\Video\Entity|null */
    $productVideo = reset($productVideos);


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


<div class="bProductDesc__ePhoto">
    <div class="bProductDesc__ePhoto-bigImg">
        <img class="bZoomedImg" src="<?= $product->getImageUrl(3) ?>" data-zoom-image="<?= $product->getImageUrl(5) ?>" alt="<?= $helper->escape($product->getName()) ?>" />
    </div><!--/product big image section -->

    <div class="bPhotoAction">
        <ul class="bPhotoActionOtherAction">
            <? if ($productVideo && $productVideo->getContent()): ?>
                <li class="bPhotoActionOtherAction__eVideo">
                    <a href="#"></a>
                    <div id="productVideo" class="blackPopup blackPopupVideo">
                        <div class="close"></div>
                        <div class="productVideo_iframe"><?= $productVideo->getContent() ?></div>
                    </div>
                </li>
            <? endif ?>
            <? if ((bool)$product->getPhoto3d() || $model3dExternalUrl || $model3dImg):  ?>
                <?
                if ($model3dExternalUrl) {
                    $class3D = 'maybe3d';
                } else if ($model3dImg) {
                    $class3D = '3dimg';
                } else {
                    $class3D = 'our3d';
                }
                ?>
                <li class="bPhotoActionOtherAction__eGrad360 <?=$class3D?>"><a href=""></a></li>
            <? endif ?>
        </ul><!--/view product section -->

        <? if (count($product->getPhoto()) > 1): ?>
            <div class="bPhotoActionOtherPhoto">
                <div class="bPhotoActionOtherPhoto__eWrappSlider">
                    <ul id="productImgGallery" class="bPhotoActionOtherPhotoList clearfix">
                        <? $i = 0; foreach ($product->getPhoto() as $photo): ?>
                            <li class="bPhotoActionOtherPhotoItem">
                                <a class="bPhotoActionOtherPhotoItem__eLink<? if (0 == $i): ?> mActive<? endif ?>" data-zoom-image="<?= $photo->getUrl(5) ?>" data-image="<?= $photo->getUrl(3) ?>" href="#">
                                    <img src="<?= $photo->getUrl(0) ?>" alt="<?= $helper->escape($product->getName()) ?>" />
                                </a>
                            </li>
                            <? $i++; endforeach ?>
                    </ul>
                </div>

                <div class="bPhotoActionOtherPhoto__eBtn mPrev"><span>&#9668;</span></div>
                <div class="bPhotoActionOtherPhoto__eBtn mNext"><span>&#9658;</span></div>
            </div><!--/slider mini product images -->
        <? endif ?>

    </div>
</div><!--/product images section -->

<? };