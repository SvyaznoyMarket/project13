<?
/**
 * @var $page \View\DefaultLayout
 * @var $menu \Model\Menu\Entity[]|\Model\Menu\BasicMenuEntity[]
 */
$lastMenu1 = end($menu); // последний элемент главного меню
$helper = new \Helper\TemplateHelper();

// ссылки на получение рекомендаций для каждого элемента меню 1-го уровня
$recommendUrlsByMenuId = [];
foreach ($menu as $menu1) {
    if (!\App::config()->mainMenu['recommendationsEnabled']) break;
    if (!$menu1->id || !(bool)$menu1->children) continue;

    try {
        $childIds = [];
        foreach ($menu1->children as $child) {
            if (!$child->id) continue;

            $childIds[] = $child->id;
        }

        $recommendUrlsByMenuId[$menu1->id] = $page->url(
            'mainMenu.recommendation',
            [
                'rootCategoryId' => $menu1->id,
                'childIds'       => implode(',', $childIds)
            ]
        );
    } catch (\Exception $e) {
        \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['main_menu', 'recommendation']);
    }
}

$hideMenu = \App::abTest()->isMenuHamburger() ? '' : '';
$hamburgerJsClass = \App::abTest()->isMenuHamburger() ? ' jsHamburgerIcon ' : '';
?>

<!-- навигация -->
<div class="header_b" <?= $hideMenu ?>>

    <div class="header-ddnav-wrap">
        <span class="header-icon-ddnav <?= $hamburgerJsClass ?>"></span>

        <nav class="header-ddnav-box">
            <ul class="navsite js-mainmenu-level1">
                <? foreach ($menu as $menu1): ?>
                    <?
                    $recommendUrl = ($menu1->id && !empty($recommendUrlsByMenuId[$menu1->id])) ? $recommendUrlsByMenuId[$menu1->id] : null;
                    ?>
                    <li class="navsite_i <?= ((bool)$menu1->children) ? 'navsite_i-child' : '' ?> <?= $lastMenu1 == $menu1 ? 'navsite_i-last': '' ?> js-mainmenu-level1-item" <? if ($recommendUrl): ?> data-recommend-url="<?= $recommendUrl ?>"<? endif ?>>
                        <? if ($menu1->char) : ?>
                            <a href="<?= $menu1->link ?>" class="navsite_lk">
                                <div class="navsite_icon"><?= $menu1->char?></div>
                                <span class="navsite_tx"><?= $menu1->name?></span>
                            </a>
                        <? else : ?>
                            <a href="<?= $menu1->link ?>" class="navsite_lk">
                                <div class="navsite_imgw"><img class="navsite_img" src="<?= $menu1->image ?>" alt=""></div>
                                <span class="navsite_tx"><?= $menu1->name?></span>
                            </a>
                        <? endif ?>

                        <? if (!empty($menu1->children)) : ?>

                            <ul class="navsite2 navsite2-new js-mainmenu-level2">

                                <? foreach ((array)$menu1->children as $menu2) : ?>
                                    <li class="navsite2_i <?= ((bool)$menu2->children) ? 'navsite2_i-child' : '' ?> js-mainmenu-level2-item">

                                        <? if ($menu2->logo) : ?>
                                            <a href="<?= $menu2->link ?>" class="navsite2_lk"><img src="<?= $menu2->logo ?>" alt="<?= $menu2->name ?>"/></a>
                                        <? else : ?>
                                            <a href="<?= $menu2->link ?>" class="navsite2_lk"><span class="navsite2_tx"><?= $menu2->name ?></span></a>
                                        <? endif ?>

                                        <? if (true || !empty($menu2->children)) : ?>
                                            <ul class="navsite3 js-mainmenu-level3">
                                                <li class="navsite3_i navsite3_i-tl"><?= $menu2->name ?></li>
                                                <? foreach ((array)$menu2->children as $menu3) : ?>
                                                    <li class="navsite3_i"><a href="<?= $menu3->link ?>" class="navsite3_lk"><?= $menu3->name ?></a></li>
                                                <? endforeach ?>

                                                <li class="navsite3_i jsMenuRecommendation"<? if ($menu2->id): ?> data-parent-category-id="<?= $menu2->id ?>"<? endif ?>></li>
                                            </ul>
                                        <? endif ?>

                                    </li>
                                <? endforeach ?>

                            </ul>

                        <? endif ?>

                    </li>
                <? endforeach ?>
            </ul>
        </nav>
    </div>
</div>
<!-- /навигация -->
