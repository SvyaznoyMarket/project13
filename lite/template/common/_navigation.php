<?
/**
 * @var $page \View\DefaultLayout
 * @var $menu \Model\Menu\Entity[]|\Model\Menu\BasicMenuEntity[]
 */
$lastMenu1 = end($menu); // последний элемент главного меню
$helper = new \Helper\TemplateHelper();
?>

<nav class="site-menu">

    <? foreach ($menu as $menu1): ?>

    <li class="site-menu__item">

        <a href="<?= $menu1->link ?>" class="site-menu__link">
        <? if ($menu1->char) : ?>
            <span class="site-menu__icon site-menu__icon_char"><?= $menu1->char ?></span>
            <span class="site-menu__text"><?= $menu1->name ?></span>
        <? else : ?>
            <span class="site-menu__icon site-menu__icon_img">
                <img src="<?= $menu1->image ?>" alt="<?= $menu1->name ?>" class="site-menu__img">
            </span>
            <span class="site-menu__text"><?= $menu1->name ?></span>
        <? endif ?>
        </a>

        <? if (!empty($menu1->children)) : ?>

            <ul class="site-menu-sub menu-hide">

            <? foreach ($menu1->children as $menu2) : ?>

                <li class="site-menu-sub__item">
                    <a href="<?= $menu2->link ?>" class="site-menu-sub__link"><?= $menu2->name ?></a>

                    <? if (!empty($menu2->children)) : ?>

                        <ul class="site-menu-2sub">

                        <? foreach ($menu2->children as $menu3) : ?>
                            <li class="site-menu-2sub__item">
                                <a href="<?= $menu3->link ?>" class="site-menu-2sub__link"><?= $menu3->name ?></a>
                            </li>
                        <? endforeach ?>

                        <!-- <li class="site-menu-2sub__item site-menu-2sub__item_wow">
                            <div class="menu-wow">
                                <div class="goods">
                                    <div class="sticker">Товар дня</div>

                                    <a href="" class="goods__img">
                                        <img src="http://9.imgenter.ru/uploads/media/ca/9c/a7/thumb_3a5a_product_160.jpeg" alt="" class="">
                                    </a>

                                    <div class="goods__name">
                                        <a href="">Чехол-книжка для Nokia Lumia 930 Cellularline (21543)</a>
                                    </div>

                                    <div class="goods__price-old"><span class="line-through">45 990</span> ₽</div>
                                    <div class="goods__price-now">45 990 ₽</div>

                                    <a class="goods__btn btn-buy" href="">Купить</a>
                                </div>
                            </div>
                        </li> -->

                        </ul>

                    <? endif ?>
                </li>

                <? endforeach ?>

            </ul>

        <? endif ?>
    </li>

    <? endforeach ?>

</nav>
