<?php
/**
 * @var $regions  \Model\Region\Entity[]
 */
?>

<?
$page = new \Templating\HtmlLayout();
$user = \App::user();
$currentRegion = $user->getRegion();
$colCount = 4;
$rowCount = 13;
$count = count($regions);
?>

<div class="popup popupRegion clearfix jsRegionPopup" style="display:none" data-current-region-id="<?= $page->escape($currentRegion->getId()) ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>" data-autocomplete-url="<?= $page->url('region.autocomplete') ?>">
    <a href="#" class="close">Закрыть</a>
    <div class="popuptitle">Ваш город</div>
    <p class="font14 popupRegion__eTopText">Выберите город, в котором собираетесь получать товары.<br/>
От выбора зависит стоимость товаров и доставки.</p>
    <form class="ui-css popupRegion__eForm">
        <input id="jscity" placeholder="Название города" class="bBuyingLine__eText font18" value="<?= $currentRegion->getName() ?>" />
        <a class="inputClear jsRegionInputClear" href="#">&times;</a>
        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton" />

        <div id="jscities" class="bSelectRegionDd" style="position:relative"></div>
    </form>
    <div class="cityInline font12 clearfix jsCityInline">
        <? if (!in_array($currentRegion->getId(), [14974, 108136])): ?>
            <div class="cityItem mAutoresolve jsAutoresolve"><a href="<?= $page->url('region.change', ['regionId' => $currentRegion->getId()]) ?>"><?= $currentRegion->getName() ?></a></div>
        <? endif ?>
        <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 14974]) ?>">Москва</a></div>
        <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 108136]) ?>">Санкт-Петербург</a></div>
        <div class="cityItem"><a class="moreCity jsRegionListMoreCity" href="#">Еще города</a></div>
    </div>
    
    <div class="regionSlidesWrap fl jsRegionSlidesWrap">
        <div class="regionSlides__inner">
            <div class="regionSlides clearfix jsRegionSlidesHolder">
                <!-- 2 слайда по 4 колонки -->
                <div class="regionSlides_slide jsRegionOneSlide">
                        <div class="colomn font12">
                        <?php
                            /** @var $region  \Model\Region\Entity */
                            $cols = 0; $rows = 0; $i = 0; foreach ($regions as $region): $i++;  $rows++;
                        ?>
                            <a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>"><?= $region->getName(); ?></a>
                            <?php if ($i == $count) break; ?>
                            <?php if ($rows == $rowCount): $rows = 0; $cols++;?>
                        </div>
                                <?php if ($cols == $colCount): $cols = 0; ?>
                </div>
                <div class="regionSlides_slide">
                                <?php endif ?>
                        <div class="colomn font12">
                            <?php endif ?>
                        <?php endforeach ?>
                        </div>
                </div>
            </div>
        </div>
        <div class="BlackArrow fl leftArr jsRegionArrow jsRegionArrowLeft" data-dir="+"></div>
        <div class="BlackArrow fl rightArr jsRegionArrow jsRegionArrowRight" data-dir="-"></div>
    </div>
    </div>
