<?
/**
 * @var $page \View\LiteLayout
 */
?>

<div class="header js-userbar">
    <div class="wrapper table">
        <div class="header__left table-cell">
            <div class="header__line header__line_top">
                <a href="" class="location dotted js-popup-show js-change-region" data-popup="region"><?= App::user()->getRegion()->getName() ?></a>
            </div>

            <div class="header__line header__line_bottom">
                <a href="/" class="logotype"></a>

                <div class="nav-section header__line-left js-navigation-menu-holder" >
                    <a href="" class="nav-section__btn btn-primary"
                       onclick="return false">Каталог товаров</a>
                    <?= $page->blockNavigation() ?>
                </div>
            </div>
        </div>

        <div class="header__center table-cell">
            <div class="header__center-inn">
                <div class="header__line header__line_top">
                    <ul class="header-shop-info">
                        <li class="header-shop-info__item"><a href="/sordex-shops" class="header-shop-info__link underline">Самовывоз</a></li>
                        <li class="header-shop-info__item"><a href="/sordex-delivery" class="header-shop-info__link underline">Доставка</a></li>
                        <li class="header-shop-info__item"><a href="/sordex-payment" class="header-shop-info__link underline">Оплата</a></li>
                    </ul>

                    <div class="phone">
                        <span class="phone__text"><?= \App::config()->company['phone'] ?></span>

                        <? if (false) : ?>
                        <a href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62" title="" class="phone-order" onclick="window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false;">
                            <span class="phone-order__text dotted">Звонок с сайта</span>
                        </a>
                        <? endif ?>
                    </div>
                </div>

                <div class="header__line header__line_bottom">
                    <?= $page->render('common/_search') ?>
                </div>
            </div>
        </div>

        <ul class="user-controls table-cell">
            <?= $page->render('common/userbar/_compare') ?>
            <?= $page->render('common/userbar/_user') ?>
            <?= $page->blockAuth() ?>
            <?= $page->render('common/userbar/_cart') ?>
        </ul>
    </div>
</div>

<?= $page->blockFixedUserbar() ?>
