<?
$page = new \Templating\HtmlLayout();
$user = \App::user();
$currentRegion = $user->getRegion();

$exampleCities1 = [
    14974 => 'Москва',
    108136 => 'Санкт-Петербург',
];

$exampleCities2 = [
    93750 => 'Новосибирск',
    93751 => 'Екатеринбург',
    99958 => 'Нижний Новгород',
    124229 => 'Казань',
    93752 => 'Челябинск',
    152593 => 'Омск',
    93749 => 'Самара',
    119623 => 'Ростов-на-Дону',
    154049 => 'Уфа',
    152599 => 'Красноярск',
    124228 => 'Пермь',
    18074 => 'Воронеж',
    143707 => 'Волгоград',
    124190 => 'Краснодар',
    124201 => 'Саратов',
    152586 => 'Тюмень',
    126766 => 'Тольятти',
];

$exampleCities2Count = count($exampleCities2);
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
            <div class="cityDesc">Например: </div>
            <? if (!array_key_exists($currentRegion->getId(), $exampleCities1) && !array_key_exists($currentRegion->getId(), $exampleCities2)): ?>
                <span class="cityItem mAutoresolve jsAutoresolve jsChangeRegionLink" data-region-id="<?= $page->escape($currentRegion->getId()) ?>"><?= $page->escape($currentRegion->getName()) ?></span>,
            <? endif ?>

            <? foreach ($exampleCities1 as $cityId => $cityName): ?>
                <span class="cityItem jsChangeRegionLink" data-region-id="<?= $page->escape($cityId) ?>"><?= $page->escape($cityName) ?></span>,
            <? endforeach ?>

            <span class="cityItem js-regionSelection-showMoreCities">ещё</span>

            <span class="moreCities js-regionSelection-moreCities">
                <? $i = 0 ?>
                <? foreach ($exampleCities2 as $cityId => $cityName): ?>
                    <? $i++ ?>
                    <span class="cityItem jsChangeRegionLink" data-region-id="<?= $page->escape($cityId) ?>"><?= $page->escape($cityName) ?></span><? if ($i < $exampleCities2Count): ?>, <? endif ?>
                <? endforeach ?>
            </span>
        </div>

        <input id="jschangecity" type="submit" value="Сохранить" class="button bigbutton" />

        <div id="jscities" class="bSelectRegionDd" style="position:relative"></div>
    </form>
</div>