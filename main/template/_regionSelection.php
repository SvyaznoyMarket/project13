<?
$page = new \Templating\HtmlLayout();
$user = \App::user();
$currentRegion = $user->getRegion();
?>

<div class="popup popupRegion clearfix jsRegionPopup" style="display:none" data-current-region-id="<?= $page->escape($currentRegion->getId()) ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>" data-autocomplete-url="<?= $page->url('region.autocomplete') ?>">
    <a href="#" class="close">&times;</a>
    <div class="popuptitle">Ваш город</div>
    <form class="ui-css popupRegion__eForm">
        <input id="jscity" placeholder="Название города" class="bBuyingLine__eText font18" value="<?= $page->escape($currentRegion->getName()) ?>" />
        <a class="inputClear jsRegionInputClear" href="#">&times;</a>
        <div class="popupRegion__city-block is-active">
            <ul class="popupRegion__city-list">
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
                <li class="popupRegion__city-name">123</li>
            </ul>
        </div>
        <div class="cityInline font12 clearfix jsCityInline">
            <div class="cityItem cityItem__desc">Например, </div>
            <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 14974]) ?>">Москва,</a></div>
            <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 108136]) ?>">Санкт-Петербург,</a></div>
            <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 93751]) ?>">Екатеринбург</a></div>
        </div>

        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton" />

        <div id="jscities" class="bSelectRegionDd" style="position:relative"></div>
    </form>


</div>