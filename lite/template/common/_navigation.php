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

?>

<nav class="site-menu">

    <? foreach ($menu as $menu1): ?>

    <li class="site-menu__item <?= $menu1->children ? 'has-child' : '' ?> js-main-menu-item js-module-require-onhover" data-module="enter.menu"
        data-recommend-url="<?= $menu1->id && !empty($recommendUrlsByMenuId[$menu1->id]) ? $recommendUrlsByMenuId[$menu1->id] : null ?>">

        <a href="<?= $menu1->link ?>" class="site-menu__link">
            <span class="site-menu__text"><?= $menu1->name ?></span>
        </a>

        <? if (!empty($menu1->children)) : ?>

            <ul class="site-menu-sub menu-hide">

            <? foreach ($menu1->children as $menu2) : ?>

                <li class="site-menu-sub__item <?= $menu2->children ? 'has-child' : '' ?>">
                    <a href="<?= $menu2->link ?>" class="site-menu-sub__link"><?= $menu2->name ?></a>

                    <? if (!empty($menu2->children)) : ?>

                        <ul class="site-menu-2sub menu-hide">

                        <? foreach ($menu2->children as $menu3) : ?>
                            <li class="site-menu-2sub__item">
                                <a href="<?= $menu3->link ?>" class="site-menu-2sub__link"><?= $menu3->name ?></a>
                            </li>
                        <? endforeach ?>

                        <li class="site-menu-2sub__item site-menu-2sub__item_wow jsMenuRecommendation"
                            data-parent-category-id="<?= $menu2->id ?$menu2->id : null ?>">
                        </li>

                        </ul>

                    <? endif ?>
                </li>

                <? endforeach ?>

            </ul>

        <? endif ?>
    </li>

    <? endforeach ?>

</nav>

<div class="nav-fader"></div>
