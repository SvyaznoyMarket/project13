<?php
/**
 * @var $page    \View\Layout
 * @var $region  \Model\Region\Entity
 * @var $regions \Model\Region\Entity[]
 */
?>

<?php
$rowCount = 3;
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
                <div class="colomn font14 clearfix">
                    <a href="/region/change/119597">Батайск</a>
                    <a href="/region/change/13241">Белгород</a>
                    <a href="/region/change/83210">Брянск</a>
                    <a href="/region/change/96423">Владимир</a>
                    <a href="/region/change/18074">Воронеж</a>
                    <a href="/region/change/1965">Долгопрудный</a>
                    <a href="/region/change/3323">Егорьевск</a>
                    <a href="/region/change/93751">Екатеринбург</a>
                    <a href="/region/change/58490">Елец</a>
                    <a href="/region/change/93747">Иваново</a>
                    <a href="/region/change/1922">Климовск</a>
                    <a href="/region/change/124190">Краснодар</a>
                    <a href="/region/change/74562">Курск</a>
                </div>
            </div>
            <div class="regionSlides_slide">
                <div class="colomn font14 clearfix">
                    <a href="/region/change/119597">Батайск</a>
                    <a href="/region/change/13241">Белгород</a>
                    <a href="/region/change/83210">Брянск</a>
                    <a href="/region/change/96423">Владимир</a>
                    <a href="/region/change/18074">Воронеж</a>
                    <a href="/region/change/1965">Долгопрудный</a>
                    <a href="/region/change/3323">Егорьевск</a>
                    <a href="/region/change/93751">Екатеринбург</a>
                    <a href="/region/change/58490">Елец</a>
                    <a href="/region/change/93747">Иваново</a>
                    <a href="/region/change/1922">Климовск</a>
                    <a href="/region/change/124190">Краснодар</a>
                    <a href="/region/change/74562">Курск</a>
                </div>
                <div class="colomn font14 clearfix">
                    <a href="/region/change/119597">Батайск</a>
                    <a href="/region/change/13241">Белгород</a>
                    <a href="/region/change/83210">Брянск</a>
                    <a href="/region/change/96423">Владимир</a>
                    <a href="/region/change/18074">Воронеж</a>
                    <a href="/region/change/1965">Долгопрудный</a>
                    <a href="/region/change/3323">Егорьевск</a>
                    <a href="/region/change/93751">Екатеринбург</a>
                    <a href="/region/change/58490">Елец</a>
                    <a href="/region/change/93747">Иваново</a>
                    <a href="/region/change/1922">Климовск</a>
                    <a href="/region/change/124190">Краснодар</a>
                    <a href="/region/change/74562">Курск</a>
                </div>
                <div class="colomn font14 clearfix">
                    <a href="/region/change/119597">Батайск</a>
                    <a href="/region/change/13241">Белгород</a>
                    <a href="/region/change/83210">Брянск</a>
                    <a href="/region/change/96423">Владимир</a>
                    <a href="/region/change/18074">Воронеж</a>
                    <a href="/region/change/1965">Долгопрудный</a>
                    <a href="/region/change/3323">Егорьевск</a>
                    <a href="/region/change/93751">Екатеринбург</a>
                    <a href="/region/change/58490">Елец</a>
                    <a href="/region/change/93747">Иваново</a>
                    <a href="/region/change/1922">Климовск</a>
                    <a href="/region/change/124190">Краснодар</a>
                    <a href="/region/change/74562">Курск</a>
                </div>
                <div class="colomn font14 clearfix">
                    <a href="/region/change/119597">Батайск</a>
                    <a href="/region/change/13241">Белгород</a>
                    <a href="/region/change/83210">Брянск</a>
                    <a href="/region/change/96423">Владимир</a>
                    <a href="/region/change/18074">Воронеж</a>
                    <a href="/region/change/1965">Долгопрудный</a>
                    <a href="/region/change/3323">Егорьевск</a>
                    <a href="/region/change/93751">Екатеринбург</a>
                    <a href="/region/change/58490">Елец</a>
                    <a href="/region/change/93747">Иваново</a>
                    <a href="/region/change/1922">Климовск</a>
                    <a href="/region/change/124190">Краснодар</a>
                    <a href="/region/change/74562">Курск</a>
                </div>
            </div>
        </div>
    </div>
    <div class="BlackArrow fl rightArr"></div>
</div>
