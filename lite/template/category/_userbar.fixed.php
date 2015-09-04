<?
/**
 * @var $category \Model\Product\Category\Entity
 */
?>

<!-- параплашка -->
<div class="header header_fix js-userbar js-userbar-fixed" style="display: none">
    <div class="wrapper table">
        <div class="header__left table-cell">
            <a href="/" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line_top">
                <ul class="bread-crumbs bread-crumbs_mini">
                    <li class="bread-crumbs__item"><a href="/catalog/electronics" class="bread-crumbs__link underline">Электроника</a></li>
                    <li class="bread-crumbs__item">Игры и консоли</li>
                </ul>
            </div>

            <div class="header__line header__line_bottom">
                <div class="fltrSet_tggl fltrSet_tggl-up js-userbar-goto-id" data-goto="productCatalog-filter-form">
                    <span class="fltrSet_tggl_tx"><a href="#productCatalog-filter-form">Бренды и параметры</a></span>
                </div>
            </div>
        </div>

        <ul class="user-controls table-cell">
            <?= $page->render('common/userbar/_compare') ?>
            <?= $page->render('common/userbar/_user') ?>
            <?= $page->render('common/userbar/_cart') ?>
        </ul>
    </div>
</div>
<!--/ параплашка -->
