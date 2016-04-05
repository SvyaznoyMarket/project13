<?
$page = new \Templating\HtmlLayout();
$user = \App::user();
$currentRegion = $user->getRegion();
?>

<div class="popup popupRegion clearfix jsRegionPopup" style="display:none" data-current-region-id="<?= $page->escape($currentRegion->getId()) ?>" data-autoresolve-url="<?= $page->url('region.autoresolve', ['nocache' => 1]) ?>" data-autocomplete-url="<?= $page->url('region.autocomplete') ?>">
    <a href="#" class="close">&times;</a>
    <div class="popuptitle">Ваш город</div>
    <p class="font14 popupRegion__eTopText">Выберите город, в котором собираетесь получать товары.<br/>
От выбора зависит стоимость товаров и доставки.</p>
    <form class="ui-css popupRegion__eForm">
        <input id="jscity" placeholder="Название города" class="bBuyingLine__eText font18" value="<?= $currentRegion->getName() ?>" />
        <a class="inputClear jsRegionInputClear" href="#">&times;</a>

        <div class="cityInline font12 clearfix jsCityInline">
            <div class="cityItem cityItem__desc">Например, </div>
            <? if (!in_array($currentRegion->getId(), [14974, 108136])): ?>
                <div class="cityItem mAutoresolve jsAutoresolve"><a href="<?= $page->url('region.change', ['regionId' => $currentRegion->getId()]) ?>"><?= $currentRegion->getName() ?></a></div>
            <? endif ?>
            <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 14974]) ?>">Москва</a></div>
            <div class="cityItem"><a class="jsChangeRegionLink" href="<?= $page->url('region.change', ['regionId' => 108136]) ?>">Санкт-Петербург</a></div>
        </div>

        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton" />

        <div id="jscities" class="bSelectRegionDd" style="position:relative"></div>
    </form>
</div>