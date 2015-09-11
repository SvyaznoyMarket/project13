<?php
/**
 * @var $regions  \Model\Region\Entity[]
 * @var $region  \Model\Region\Entity
 */
?>

<?
$page = new \Templating\HtmlLayout();
$user = \App::user();
$currentRegion = $user->getRegion();
?>
<div class="popup popup_region js-popup-region"
     data-autocomplete-url="<?= $page->url('region.autocomplete') ?>"
     data-region='<?= json_encode([ 'id' => $currentRegion->getId(), 'name'=> $currentRegion->getName() ], JSON_UNESCAPED_UNICODE) ?>'
    >

    <div class="popup__close js-popup-close"></div>

    <div class="popup__content">
        <div class="popup__title">Ваш город</div>

        <div class="popup__desc">
            Выберите город, в котором собираетесь получать товары.<br/>
            От выбора зависит стоимость товаров и доставки.
        </div>

        <form class="form form-region search-bar js-region-change-form" action="/region/change/" method="get">
            <i class="search-bar__icon i-controls i-controls--search"></i>
            <i class="search-bar__icon-clear icon-clear js-region-change-form-clear"></i>
            <input id="jscity" type="text" class="form-region__it search-bar__it it js-region-change-form-input" placeholder="Найти свой регион">
            <button class="form-region__btn btn-primary btn-primary_normal">Сохранить</button>

            <!-- саджест поиска региона -->
            <div class="region-suggest js-region-autocomplete-results"></div>
            <!--/ саджест поиска региона -->
        </form>

        <ul class="region-subst">
            <li class="region-subst__item"><a href="<?= $page->url('region.change', ['regionId' => 14974]) ?>" class="region-subst__link dotted">Москва</a></li>
            <li class="region-subst__item"><a href="<?= $page->url('region.change', ['regionId' => 108136]) ?>" class="region-subst__link dotted">Санкт-Петербург</a></li>
            <li class="region-subst__item region-subst__item_toggle"><a href="" class="region-subst__link dotted js-region-show-more-cities" >Еще города</a></li>
        </ul>

        <!-- Что бы показать слайдер регионов добавляем класс show -->
        <div class="region-slides slider-section js-region-more-cities-wrapper">
            <button class="slider-section__btn slider-section__btn_prev js-goods-slider-btn-prev-region-list"></button>

            <div class="js-slider-goods-region-list" data-slick-slider="region-list"
                 data-slick='{"infinite": false, "slidesToShow": 4, "slidesToScroll": 2, "dots": false, "prevArrow": ".js-goods-slider-btn-prev-region-list", "nextArrow": ".js-goods-slider-btn-next-region-list" }'>
                <? foreach (array_chunk($regions, 9) as $regionChunk) : ?>
                <div class="region-slides__item">
                    <ul class="region-list">
                        <? foreach ($regionChunk as $region) : ?>
                        <li class="region-list__item">
                            <a href="<?= $page->url('region.change', ['regionId' => $region->id]) ?>" class="region-list__link"><?= $region->name ?></a>
                        </li>
                        <? endforeach ?>
                    </ul>
                </div>
                <? endforeach ?>
            </div>

            <button class="slider-section__btn slider-section__btn_next js-goods-slider-btn-next-region-list"></button>
        </div>
    </div>
</div>