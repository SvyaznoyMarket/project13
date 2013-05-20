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
    <h2 class="popuptitle">Из какого вы города?</h2>
    <p class="font16">Цены товаров и доставки зависят от региона.</p>
    <form class="ui-css">
        <input id="jscity" data-url-autocomplete="<?= $page->url('region.autocomplete') ?>" placeholder="Введите свой город" class="bBuyingLine__eText font18"/>
        <a class="inputClear" href="#">x</a>
        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled"/>

        <div id="jscities" style="position:relative"></div>
    </form>
    <div class="cityInline font14 clearfix">
        <? if ($currentRegion && !in_array($currentRegion->getId(), [14974, 108136])): ?>
            <div class="cityItem"><a href="<?= $page->url('region.change', ['regionId' => $currentRegion->getId()]) ?>"><?= $currentRegion->getName() ?></a></div>
        <? endif ?>
        <div class="cityItem"><a href="<?= $page->url('region.change', ['regionId' => 14974]) ?>">Москва</a></div>
        <div class="cityItem"><a href="<?= $page->url('region.change', ['regionId' => 108136]) ?>">Санкт-Петербург</a></div>
    </div>
    <div class="BlackArrow fl leftArr"></div>
    <div class="regionSlidesWrap fl">
        <div class="regionSlides clearfix">
            <!-- 2 слайда по 4 колонки -->
            <div class="regionSlides_slide">
                    <div class="colomn font14">
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
                    <div class="colomn font14">
                        <?php endif ?>
                    <?php endforeach ?>
                    </div>
            </div>
        </div>
    </div>
    <div class="BlackArrow fl rightArr"></div>
</div>
