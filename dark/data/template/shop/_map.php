<?php
/**
 * @var $page          \View\Shop\ShowPage
 * @var $regions       \Model\Region\Entity[]
 * @var $currentRegion \Model\Region\Entity
 * @var $markers       array
 */
?>

<!-- bMapShops -->
<div class="bMapShops">
    <div class='bMapShops__eHead'>
        <div class='bMapShops__eRegion'>
            <h2 class='bMapShops__eRegionTitle'>Enter в <?= $currentRegion->getInflectedName(5) ?>!</h2>
            <div class='bMapShops__eRegionText'>Enter в регионах:</div>
            <div class="selectbox selectbox170 fl"><i></i>
                <select id="region-select" class="styled" name="region">
                <? foreach ($regions as $region): ?>
                    <option data-url="<?= $page->url('shop.region', array('regionId' => $region->getId())) ?>"<?php if ($region->getId() == $currentRegion->getId()): ?> selected="selected"<? endif ?> value="<?= $region->getId() ?>"><?= $region->getName() ?></option>
                <? endforeach ?>
                </select>
            </div>
        </div>
        <img class="bMapShops__eImage" src="/images/shop-h1.png" />
    </div>

    <? if ((bool)$markers): ?>
    <div class='bMapShops__eContent'>

        <h2 class='bMapShops__eTitle'>Магазины Enter на карте</h2>
        <div id="region_map-container" class='bMapShops__eMapWrap' style="width: auto; height: 490px;"></div>
    </div>
    <? endif ?>

</div>

<input id="map-markers" type="hidden" data-content='<?= json_encode($markers) ?>' />
<!-- /bMapShops -->

<div id="map-info_window-container" style="display:none"></div>

<script type="text/html" id="infowindowtmpl">
    <div class="bMapShops__ePopupRel">
        <h3><%=address%></h3>
        <span><%=regtime%></span><br>
        <a href="<%=link%>" class="bGrayButton shopchoose">Перейти к магазину</a>
    </div>
</script>
