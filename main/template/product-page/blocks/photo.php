<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $videoHtml,
    $properties3D
){

    $request = \App::request();

    ?>

    <!-- слайдер изображений товара -->
    <div class="product-card__l">

            <div class="product-card-photo">
                <? if ($product->getLabel()): ?>
                    <a class="product-card-photo-sticker" href=""><img src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getLabel()->getName()) ?>"></a>
                <? endif ?>
                <img src="<?= $product->getMainImageUrl('product_550') ?>"
                     class="product-card-photo__img js-photo-zoomedImg jsOpenProductImgPopup jsProductMiddlePhoto"
                     alt="<?= $helper->escape($product->getName()) ?>"
                     data-is-slot="<?= $product->getSlotPartnerOffer() ? 'true' : 'false' ?>"
                     style="cursor: zoom-in"
                    />
                <? if (!$product->isAvailable()) : ?>
                    <div class="product-card-photo__overlay">Нет в наличии</div>
                <? endif ?>
            </div>

        <!-- если картинок больше 5 добавляем класс product-card-photo-thumbs--slides -->
        <div class="product-card-photo-thumbs jsProductThumbHolder <?= count($product->getMedias('image')) > 5 ? 'product-card-photo-thumbs--slides' : ''?>"
            <? if (count($product->getMedias('image')) < 2) : ?>style="display: none"<? endif ?>
        >
            <ul class="product-card-photo-thumbs-list jsProductThumbList">
                <? foreach ($product->getMedias('image') as $key => $photo) : ?>
                    <li class="product-card-photo-thumbs__i jsProductPhotoThumb <?= $key == 0 ? 'product-card-photo-thumbs__i--act' : '' ?>"
                        data-middle-img="<?= $photo->getSource('product_500')->url ?>"
                        data-big-img="<?= $photo->getSource('product_550')->url ?>"
                        ><img src="<?= $photo->getSource('product_500')->url ?>" class="product-card-photo-thumbs__img" /></li>
                <? endforeach ?>
            </ul>

            <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--l product-card-photo-thumbs__btn--disabled jsProductThumbBtn" data-dir="+="></div>
            <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--r jsProductThumbBtn" data-dir="-="></div>
        </div>

        <ul class="product-card-media jsProductMediaButton">
            <? if ($product->hasVideo()) : ?>
                <li class="product-card-media__i product-card-media__i--video"></li>
                <!-- Попап видео -->
                <div class="popup popup--skinny" style="display: none">
                    <i class="closer jsPopupCloser">×</i>
                    <?= $videoHtml ?>
                </div>
                <!--/ Попап видео-->
            <? endif ?>
            <? if ($product->has3d()) : ?>
                <li class="product-card-media__i product-card-media__i--3d"></li>
                <!-- Попап 3D -->
                <div class="popup popup--skinny" style="display: none">
                    <i class="closer jsPopupCloser">×</i>
                    <? if ($properties3D['type'] == 'swf') : ?>
                        <div class="jsProduct3DContainer" style="position: relative" data-url="<?= $helper->escape($properties3D['url']); ?>" data-type="swf">
                            <div id="js-product-3d-swf-popup-model">
                                <!-- Fallback -->
                                <a href="http://www.adobe.com/go/getflashplayer">
                                    <img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" />
                                </a>
                                <!--/ Fallback -->
                            </div>
                        </div>
                    <? elseif ($product->json3d) : ?>
                        <div class="jsProduct3DJSON" data-value="<?= $helper->json($product->json3d); ?>" data-host="<?= $helper->json(['http://' . App::request()->getHost()]) ?>"></div>
                    <? endif ?>
                </div>
                <!--/ Попап 3D -->
            <? endif ?>
        </ul>

        <!-- попап просмотра большого изображения -->
        <div class="popup popup--photo jsProductImgPopup" style="display: none">
            <i class="closer jsPopupCloser">×</i>

            <div class="product-card-photo jsProductPopupBigPhotoHolder">
                <!-- <div class="product-card-photo__img" style="height: 620px; margin: 0 auto; overflow: hidden; width: 785px"> -->
                <img src="" class="product-card-photo__img jsProductPopupBigPhoto" style="height: 620px; position: relative; top: 0; left: 0;" />
                <!-- </div> -->
                <div class="product-card-photo__ctrl product-card-photo__ctrl--prev jsProductPopupSlide" data-dir="-1"><span class="symb"></span></div>
                <div class="product-card-photo__ctrl product-card-photo__ctrl--next jsProductPopupSlide" data-dir="1"><span class="symb"></span></div>

                <div class="product-card-photo-zoom">
                    <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--in jsProductPopupZoom" data-dir="+1">+</div>
                    <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--out jsProductPopupZoom" data-dir="-1">–</div>
                </div>
            </div>

            <div class="product-card-photo-thumbs" style="max-width: 670px;">
                <ul class="product-card-photo-thumbs-list">
                    <? foreach ($product->getMedias('image') as $key => $photo) : ?>
                        <li class="product-card-photo-thumbs__i jsPopupPhotoThumb"
                            data-big-img="<?= $photo->getSource('product_1500')->url ?>"
                            ><img src="<?= $photo->getSource('product_120')->url ?>" class="product-card-photo-thumbs__img"></li>
                    <? endforeach ?>
                </ul>

                <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--l product-card-photo-thumbs__btn--disabled"></div>
                <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--r"></div>
            </div>

            <?= $helper->render('cart/__button-product', [
                'product'  => $product,
                'sender'   => ($request->get('sender') ? (array)$request->get('sender') : []) + ['name' => null, 'method' => null, 'position' => null],
                'location' => 'userbar',
                'sender2'  => '',
            ]) // Кнопка купить ?>

        </div>
        <!--/ попап просмотра большого изображения -->
    </div>

<?}; return $f;