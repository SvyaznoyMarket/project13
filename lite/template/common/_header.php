<?
/**
 * @var $page \View\LiteLayout
 */
?>

<div class="header js-userbar">
    <div class="wrapper table">
        <div class="header__side header__logotype table-cell">
            <a href="/" class="logotype"></a>
        </div>

        <div class="header__center table-cell">
            <div class="header__line header__line_top">
                <a href="" class="location dotted js-popup-show jsRegionSelection" data-popup="region"><?= \App::user()->getRegion()->getName() ?></a>

                <ul class="header-shop-info">
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Магазины и самовывоз</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Доставка</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Оплата</a></li>
                    <li class="header-shop-info__item"><a href="" class="header-shop-info__link underline">Партнерам</a></li>
                </ul>

                <div class="phone">
                    <span class="phone__text">+7 495 775-00-06</span>

                    <a href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62" title="" class="phone-order" onclick="window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false;">
                        <span class="phone-order__text dotted">Звонок с сайта</span>
                    </a>
                </div>
            </div>

            <div class="header__line header__line_bottom">

                <?= $page->render('common/_search') ?>

                <ul class="user-controls">

                    <?= $page->render('common/userbar/_compare') ?>
                    <?= $page->render('common/userbar/_user') ?>

                </ul>
            </div>
        </div>

        <?= $page->render('common/userbar/_cart') ?>

    </div>
</div>

<?= $page->blockFixedUserbar() ?>
