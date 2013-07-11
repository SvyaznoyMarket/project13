<?php
/**
 * @var $helper             \Helper\TemplateHelper
 * @var $page               \View\Product\IndexPage
 * @var $product            \Model\Product\Entity
 * @var $productVideos      \Model\Product\Video\Entity[]
 * @var $productVideo       \Model\Product\Video\Entity|null
 * @var $user               \Session\User
 * @var $creditData         array
 */
?>

<div class="bProductDesc clearfix">

    <div class="bProductDesc__ePhoto">
        <div class="bProductDesc__ePhoto-bigImg">
            <img class="bZoomedImg" src="<?= $product->getImageUrl(3) ?>" data-zoom-image="<?= $product->getImageUrl(4) ?>" alt="<?= $page->escape($product->getName()) ?>" />
        </div><!--/product big image section -->

        <div class="bPhotoAction">
            <ul class="bPhotoActionOtherAction">
                <? if ($productVideo && $productVideo->getContent()): ?>
                    <li class="bPhotoActionOtherAction__eVideo"><a href=""></a></li>
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

            <? if (count($photoList) > 1): ?>
            <div class="bPhotoActionOtherPhoto">
                <div class="bPhotoActionOtherPhoto__eWrappSlider">
                    <ul id="productImgGallery" class="bPhotoActionOtherPhotoList clearfix">
                        <? $i = 0; foreach ($photoList as $photo): ?>
                            <li class="bPhotoActionOtherPhotoItem">
                                <a class="bPhotoActionOtherPhotoItem__eLink<? if (0 == $i): ?> mActive<? endif ?>" data-zoom-image="<?= $photo->getUrl(4) ?>" data-image="<?= $photo->getUrl(3) ?>" href="#">
                                    <img src="<?= $photo->getUrl(0) ?>" alt="<?= $page->escape($product->getName()) ?>" />
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

    <div class="bProductDesc__eStore">
        <?= $helper->render('product/__state', ['product' => $product]) // Есть в наличии ?>

        <?= $helper->render('product/__price', ['product' => $product]) // Цена ?>

        <?= $helper->render('product/__notification-lowerPrice', ['product' => $product]) // Узнать о снижении цены ?>

        <?= $helper->render('product/__credit', ['product' => $product, 'creditData' => $creditData]) // Беру в кредит ?>

        <div class="bProductDesc__eStore-text">
            <?= $product->getTagline() ?>
            <div class="text__eAll"><a class="jsGoToId" data-goto="productspecification" href="">Характеристики</a></div>
        </div>

        <div class="bReviewSection clearfix">
            <div class="bReviewSection__eStar">
                <? $avgStarScore = empty($reviewsData['avg_star_score']) ? 0 : $reviewsData['avg_star_score'] ?>
                <?= empty($avgStarScore) ? '' : $page->render('product/_starsFive', ['score' => $avgStarScore]) ?>
            </div>
            <? if (!empty($avgStarScore)) { ?>
                <span class="jsGoToId border" data-goto="bHeadSectionReviews"><?= $reviewsData['num_reviews'] ?> <?= $page->helper->numberChoice($reviewsData['num_reviews'], ['отзыв', 'отзыва', 'отзывов']) ?></span>
            <? } else { ?>
                <span>Отзывов нет</span>
            <? } ?>

            <span class="bReviewSection__eWrite jsLeaveReview" data-pid="<?= $product->getId() ?>">Оставить отзыв</span>

            <div style="position:fixed; top:40px; left:50%; margin-left:-442px; z-index:1002; display:none; width:700px; height:480px" class="reviewPopup popup clearfix">
                <a class="close" href="#">Закрыть</a>
                <iframe id="rframe" frameborder="0" scrolling="auto" height="480" width="700"></iframe>
            </div>
        </div><!--/review section -->

        <? if ((bool)$product->getModel() && (bool)$product->getModel()->getProperty()): //модели ?>
            <div class="bProductDesc__eStore-select">
                <? foreach ($product->getModel()->getProperty() as $property): ?>
                    <? if (false && $property->getIsImage()): ?>
                    <? else: ?>
                    <?
                        $productAttribute = $product->getPropertyById($property->getId());
                        if (!$productAttribute) break;
                    ?>

                    <? endif ?>
                    <div class="bDescSelectItem clearfix">
                        <strong class="bDescSelectItem__eName"><?= $property->getName() ?></strong>

                        <span class="bDescSelectItem__eValue"><?= $productAttribute->getStringValue() ?></span>

                        <select class="bDescSelectItem__eSelect">
                            <? foreach ($property->getOption() as $option): ?>
                                <option class="bDescSelectItem__eOption" value="<?= $option->getHumanizedName() ?>" data-url="<?= $option->getProduct()->getLink() ?>"<? if ($option->getValue() == $productAttribute->getValue()): ?> selected="selected"<? endif ?>><?= $option->getHumanizedName() ?></option>
                            <? endforeach ?>
                        </select>
                    </div>
                <? endforeach ?>

            </div><!--/additional product options -->
        <? endif ?>

    </div><!--/product shop description box -->
</div><!--/product shop description section -->