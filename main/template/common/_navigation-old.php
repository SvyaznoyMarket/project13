<?
/**
 * @var $menu \Model\Menu\Entity[]|\Model\Menu\BasicMenuEntity[]
 */
?>

<?
if (!isset($lastMenu1)) $lastMenu1 = null;
?>

<div id="header" class="clearfix">
    <a id="topLogo" href="/">Enter Связной</a>

    <!-- навигация -->
    <nav class="header_nav">
        <ul class="navsite js-mainmenu-level1">
            <? foreach ($menu as $menu1) : ?>
                <li class="navsite_i <?= ((bool)$menu1->children) ? 'navsite_i-child' : '' ?> <?= $lastMenu1 == $menu1 ? 'navsite_i-last': '' ?> js-mainmenu-level1-item">
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

                        <ul class="navsite2 js-mainmenu-level2">

                            <? foreach ($menu1->children as $menu2) : ?>
                                <li class="navsite2_i <?= ((bool)$menu2->children) ? 'navsite2_i-child' : '' ?> js-mainmenu-level2-item">

                                    <? if ($menu2->logo) : ?>
                                        <a href="<?= $menu2->link ?>" class="navsite2_lk"><img src="<?= $menu2->logo ?>" alt="<?= $menu2->name ?>"/></a>
                                    <? else : ?>
                                        <a href="<?= $menu2->link ?>" class="navsite2_lk"><?= $menu2->name ?></a>
                                    <? endif ?>

                                    <? if (!empty($menu2->children)) : ?>
                                        <ul class="navsite3 js-mainmenu-level3">
                                            <li class="navsite3_i navsite3_i-tl"><?= $menu2->name ?></li>
                                            <li class="navsite3_i navsite3_i-img">
                                                <img data-src="<?= $menu2->getImagePath() ?>" alt="<?= $menu2->name ?>" class="menuImgLazy">
                                                <noscript><img src="<?= $menu2->getImagePath() ?>" alt="<?= $menu2->name ?>"></noscript>
                                            </li>
                                            <? foreach ($menu2->children as $menu3) : ?>
                                                <li class="navsite3_i"><a href="<?= $menu3->link ?>" class="navsite3_lk"><?= $menu3->name ?></a></li>
                                            <? endforeach ?>
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
<!-- /навигация -->