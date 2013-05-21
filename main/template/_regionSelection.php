<?php
/**
 * @var $page     \View\Layout
 * @var $user     \Session\User
 * @var $regions  \Model\Region\Entity[]
 */
?>

<?
$currentRegion = $user->getRegion();

$colCount = 4;
$rowCount = 13;
$count = count($regions);
?>

<div class="popup popupRegion clearfix" style="display:none">
    <a href="#" class="close">Закрыть</a>
    <h2 class="popuptitle">Ваш город</h2>
    <p class="font14 popupRegion__eTopText">Выберите город, в котором собираетесь получать товары.<br/>
От выбора зависит стоимость товаров и доставки.</p>
    <form class="ui-css popupRegion__eForm">
        <input id="jscity" data-url-autocomplete="<?= $page->url('region.autocomplete') ?>" placeholder="Название города" class="bBuyingLine__eText font18" value="<?= $currentRegion->getName() ?>" />
        <a class="inputClear" href="#">&times;</a>
        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled"/>

        <div id="jscities" style="position:relative"></div>
    </form>
    <div class="cityInline font12 clearfix">
        <? if (!in_array($currentRegion->getId(), [14974, 108136])): ?>
            <div class="cityItem mAutoresolve"><a href="<?= $page->url('region.change', ['regionId' => $currentRegion->getId()]) ?>"><?= $currentRegion->getName() ?></a></div>
        <? endif ?>
        <div class="cityItem"><a href="<?= $page->url('region.change', ['regionId' => 14974]) ?>">Москва</a></div>
        <div class="cityItem"><a href="<?= $page->url('region.change', ['regionId' => 108136]) ?>">Санкт-Петербург</a></div>
        <div class="cityItem"><a class="moreCity" href="#">Еще города</a></div>
    </div>
    
    <div class="regionSlidesWrap fl">
        <div class="regionSlides clearfix">
            <!-- 2 слайда по 4 колонки -->
            <div class="regionSlides_slide">
                    <div class="colomn font12">
                    <?php
                        /** @var $region  \Model\Region\Entity */
                        $cols = 0; $rows = 0; $i = 0; foreach ($regions as $region): $i++;  $rows++;
                    ?>
                        <a href="<?= $page->url('region.change', ['regionId' => $region->getId()]) ?>"><?= $region->getName(); ?></a>
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
        <div class="BlackArrow fl leftArr"></div>
        <div class="BlackArrow fl rightArr"></div>
    </div>
    </div>
