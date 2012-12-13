<?php
/**
 * @var $page                 \View\Shop\ShowPage
 * @var $shopAvailableRegions \Model\Region\Entity[]
 * @var $currentRegion        \Model\Region\Entity
 * @var $markers              array
 */
?>

<!-- bMapShops -->
<div class="bMapShops">
    <div class='bMapShops__eHead'>
        <div class='bMapShops__eRegion font14 width290'>
            <p>У нас 58 магазинов в 32 городах России.</p>
            <p>А доставить вашу покупку мы можем даже туда, где нас нет: во многих городах нам помогают с доставкой проверенные транспортные компании.</p>

        </div>
        <img class="bMapShops__eImage" src="/images/shop-h1.png" alt="shopPageLogo" />
    </div>

    <? if ((bool)$markers): ?>
    <h2 class='bMapShops__eTitle'><a href="#">Магазины Enter на карте</a></h2>
    <script type="text/html" id="shopInCity">
        <li class="shopInCity" ref="<%=id%>"><p><%=name%></p>
            <span><%=regtime%></span><br>
            <a href="<%=link%>" class="bGrayButton shopchoose">Перейти к магазину</a>
        </li>
    </script>
    <div class='bMapShops__eContent'>
        <? if ((bool)$shopAvailableRegions): ?>
        <ul style="width: 218px;" class="bMapShops__eMapCityList fl">
            <? foreach ($shopAvailableRegions as $region): ?>
            <li class="font16 bMapShops__eMapCityList_city" ref="<?=$region->getId()?>">
                <?= $region->getName() ?>
                <ul class="font14">
                </ul>
            </li>
            <? endforeach ?>
        </ul>
        <? endif ?>
        <div id="region_map-container" class='bMapShops__eMapWrap' style="height: 490px;"></div>
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
