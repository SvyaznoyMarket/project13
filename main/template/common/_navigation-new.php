<?
/**
 * @var $menu \Model\Menu\Entity[]|\Model\Menu\BasicMenuEntity[]
 */
$lastMenu1 = end($menu); // последний элемент главного меню
?>

<!-- навигация -->
<nav class="header_b">
    <ul class="navsite">
    <? foreach ($menu as $menu1) : ?>
        <li class="navsite_i <?= ((bool)$menu1->children) ? 'navsite_i-child' : '' ?> <?= $lastMenu1 == $menu1 ? 'navsite_i-last': '' ?>" data-id="<?= $menu1->id ?>">
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
            <? endif; ?>

            <? if (!empty($menu1->children)) : ?>

            <ul class="navsite2">

                <? foreach ($menu1->children as $menu2) : ?>
                    <li class="navsite2_i <?= ((bool)$menu2->children) ? 'navsite2_i-child' : '' ?>">

                        <? if ($menu2->logo) : ?>
                            <a href="<?= $menu2->link ?>" class="navsite2_lk"><img src="<?= $menu2->logo ?>" alt="<?= $menu2->name ?>"/></a>
                        <? else : ?>
                            <a href="<?= $menu2->link ?>" class="navsite2_lk"><span class="navsite2_tx"><?= $menu2->name ?></span></a>
                        <? endif; ?>

                        <? if (!empty($menu2->children)) : ?>
                            <ul class="navsite3">
                                <li class="navsite3_i navsite3_i-tl"><?= $menu2->name ?></li>
                                <? foreach ($menu2->children as $menu3) : ?>
                                    <li class="navsite3_i"><a href="<?= $menu3->link ?>" class="navsite3_lk"><?= $menu3->name ?></a></li>
                                <? endforeach; ?>

                                <li class="navsite3_i">
                                    <div class="navitem">
                                        <div class="navitem_tl">ТОВАР ДНЯ</div>
                                        <a href="" class="navitem_cnt">
                                            <img src="http://fs09.enter.ru/6/1/163/59/240911.jpg" alt="" class="navitem_img">
                                            <span class="navitem_n">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</span>
                                        </a>
                                        <div class="navitem_pr">
                                            22 990 <span class="rubl">p</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        <? endif; ?>

                    </li>
                <? endforeach; ?>

            </ul>

            <? endif; ?>

        </li>
    <? endforeach; ?>
    </ul>
</nav>
<!-- /навигация -->
