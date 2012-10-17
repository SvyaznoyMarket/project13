<?php
/**
 * @var $page \View\Layout
 * @var $region \Model\Region\Entity
 */
?>

<?php
$rowCount = 3;
$regions = RepositoryManager::getRegion()->getShopAvailableCollection();

$columnCount = array();
$count = count($regions);

for ($i = 0; $i < $rowCount; $i++) {
    $columnCount[$i] = (int)floor($count / $rowCount) + (($count % $rowCount) > $i ? 1 : 0);
}
?>

<div class="popupRegion" style="display:none">
    <a href="#" class="close">Закрыть</a>

    <h2 class="pouptitle">Привет! Укажите, из какого вы города.</h2>

    <div class="hidden">
        <p>скрытый блок</p>
    </div>
    <form class="ui-css">
        <input id="jscity" data-url-autocomplete="<?= $page->url('region.autocomplete') ?>" placeholder="Введите свой город" class="bBuyingLine__eText mInputLong"/>
        <a class="inputClear" href="#">x</a>
        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton mDisabled"/>

        <div id="jscities" style="position:relative"></div>
    </form>
    <div class="cityInline">
        <a href="<?= $page->url('region.change', array('regionId' => 14974)) ?>">Москва</a>
        <a href="<?= $page->url('region.change', array('regionId' => 108136)) ?>">Санкт-Петербург</a>
    </div>

    <? $offset = 0; foreach ($columnCount as $count): ?>
        <div class="colomn">
            <? for ($i = 0; $i < $count; $i++) { ?>
            <? $region = $regions[$offset + $i] ?>
            <a href="<?= $page->url('region.change', array('regionId' => $region->getId())) ?>"><?= $region->getName(); ?></a>
            <? } ?>
        </div>
    <? $offset += $count; endforeach ?>

    <div class="clear"></div>
</div>
