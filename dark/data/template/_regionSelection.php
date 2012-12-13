<?php
/**
 * @var $page    \View\Layout
 * @var $region  \Model\Region\Entity
 * @var $regions \Model\Region\Entity[]
 */
?>

<?php
$rowCount = 4;
$columnCount = array();
$count = count($regions);

for ($i = 0; $i < $rowCount; $i++) {
    $columnCount[$i] = (int)floor($count / $rowCount) + (($count % $rowCount) > $i ? 1 : 0);
}
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
        <div class="cityItem"><a href="<?= $page->url('region.change', array('regionId' => 14974)) ?>">Москва</a></div>
        <div class="cityItem"><a href="<?= $page->url('region.change', array('regionId' => 108136)) ?>">Санкт-Петербург</a></div>
    </div>
    <div class="BlackArrow fl leftArr"></div>
    <div class="regionSlidesWrap fl">
        <div class="regionSlides">
            <!-- 2 слайда по 4 колонки -->
            <div class="regionSlides_slide">
                <? $offset = 0; foreach ($columnCount as $count): ?>
                    <div class="colomn font14">
                        <? for ($i = 0; $i < $count; $i++) { ?>
                        <? $region = $regions[$offset + $i] ?>
                        <a href="<?= $page->url('region.change', array('regionId' => $region->getId())) ?>"><?= $region->getName(); ?></a>
                        <? } ?>
                    </div>
                <? $offset += $count; endforeach ?>
            </div>
<!--             <div class="regionSlides_slide">

            </div> -->
        </div>
    </div>
    <div class="BlackArrow fl rightArr"></div>
</div>
