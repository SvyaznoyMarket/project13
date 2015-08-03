<?
/**
 * @var $category \Model\Product\Category\Entity
 */
?>

<!-- параплашка -->
<div class="header header_fix js-userbar js-userbar-fixed" style="display: none">
    <div class="wrapper table">
        <div class="header__side header__logotype table-cell">
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


                <div class="fltrSet_tggl fltrSet_tggl-up">
                    <span class="fltrSet_tggl_tx">Бренды и параметры</span>
                </div>

                <ul class="user-controls">

                    <?= $page->render('common/userbar/_compare') ?>
                    <?= $page->render('common/userbar/_user') ?>

                </ul>
            </div>
        </div>

        <?= $page->render('common/userbar/_cart') ?>

    </div>
</div>
<!--/ параплашка -->
