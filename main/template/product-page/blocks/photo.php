<?
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product
){

    ?>

    <!-- слайдер изображений товара -->
    <div class="product-card__l">
        <div class="product-card-photo">
            <? if ($product->getLabel()): ?>
                <a class="product-card-photo-sticker" href=""><img src="<?= $product->getLabel()->getImageUrl(1) ?>" alt="<?= $helper->escape($product->getLabel()->getName()) ?>"></a>
            <? endif ?>
            <img src="<?= $product->getImageUrl(3) ?>"
                 class="product-card-photo__img js-photo-zoomedImg"
                 data-zoom-image="<?= $product->getImageUrl(5) ?>"
                 data-zoom-disable="false"
                 alt="<?= $helper->escape($product->getName()) ?>"
                 data-is-slot="<?= $product->getSlotPartnerOffer() ? 'true' : 'false' ?>"
                />
        </div>

        <!-- если картинок больше 5 добавляем класс product-card-photo-thumbs--slides -->
        <div class="product-card-photo-thumbs <?= count($product->getPhoto()) > 5 ? 'product-card-photo-thumbs--slides' : ''?>">
            <ul class="product-card-photo-thumbs-list">
                <? foreach ($product->getPhoto() as $key => $photo) : ?>
                    <li class="product-card-photo-thumbs__i jsOpenProductImgPopup jsProductPhotoThumb <?= $key == 0 ? 'product-card-photo-thumbs__i--act' : '' ?>"
                        data-big-img="<?= $photo->getUrl(5) ?>"
                        ><img src="<?= $photo->getUrl() ?>" class="product-card-photo-thumbs__img" /></li>
                <? endforeach ?>
            </ul>

            <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--l product-card-photo-thumbs__btn--disabled"></div>
            <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--r"></div>

        </div>

        <ul class="product-card-media">
            <? if ($product->hasVideo()) : ?>
                <li class="product-card-media__i product-card-media__i--video"></li>
            <? endif ?>
            <? if ($product->has3d()) : ?>
                <li class="product-card-media__i product-card-media__i--3d"></li>
            <? endif ?>
        </ul>

        <!-- попап просмотра большого изображения -->
        <div class="popup popup--photo jsProductImgPopup" style="display: none">
            <i class="closer">×</i>

            <div class="product-card-photo">
                <div class="product-card-photo__img jsProductPopupBigPhotoHolder" style="height: 620px; margin: 0 auto; overflow: hidden">
                    <img src="" class="jsProductPopupBigPhoto" style="height: 620px; position: relative; top: 0; left: 0;" />
                </div>
                <div class="product-card-photo__ctrl product-card-photo__ctrl--prev jsProductPopupSlide" data-dir="-1"><span class="symb"></span></div>
                <div class="product-card-photo__ctrl product-card-photo__ctrl--next jsProductPopupSlide" data-dir="+1"><span class="symb"></span></div>

                <div class="product-card-photo-zoom">
                    <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--in jsProductPopupZoom" data-dir="+1">+</div>
                    <div class="product-card-photo-zoom__ctrl product-card-photo-zoom__ctrl--out jsProductPopupZoom" data-dir="-1">–</div>
                </div>
            </div>

            <div class="product-card-photo-thumbs">
                <ul class="product-card-photo-thumbs-list">
                    <? foreach ($product->getPhoto() as $key => $photo) : ?>
                        <li class="product-card-photo-thumbs__i jsProductPhotoThumb"
                            data-big-img="<?= $photo->getUrl(5) ?>"
                            ><img src="<?= $photo->getUrl() ?>" class="product-card-photo-thumbs__img"></li>
                    <? endforeach ?>
                </ul>

                <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--l product-card-photo-thumbs__btn--disabled"></div>
                <div class="product-card-photo-thumbs__btn product-card-photo-thumbs__btn--r"></div>
            </div>

            <a href="" class="btn-type btn-type--buy">В корзину</a>
        </div>
        <!--/ попап просмотра большого изображения -->

        <!-- Попап видео -->
        <div class="popup popup--skinny" style="display: none"k>
            <i class="closer">×</i>
            <!-- видео или 3d должно быть тут -->
            <iframe src="https://player.vimeo.com/video/124139626?color=ffffff&portrait=0&badge=0" width="500" height="281" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        </div>
        <!--/ Попап видео-->

    </div>

<?}; return $f;