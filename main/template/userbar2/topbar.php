<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<div class="header_t clearfix">
    <div class="header_i hdcontacts">
        <a class="hdcontacts_lk jsRegionChange undrl" href="<?= $page->url('region.change', ['regionId' => $user->getRegion()->getId()]) ?>">Набережные челны</a>
        <div class="hdcontacts_phone">+7 (495) 775-00-06</div>
    </div>

    <a class="header_i hdcall" href="">
        <i class="i-header i-header-phone"></i>
        <span class="hdcall_tx">Звонок<br/>с сайта</span>
    </a>

    <ul class="header_i hdlk">
        <li class="hdlk_i"><a href="" class="hdlk_lk undrl">Наши магазины</a></li>
        <li class="hdlk_i"><a href="" class="hdlk_lk undrl">Доставка</a></li>
    </ul>

    <menu class="header_i userbtn">

        <li class="userbtn_i userbtn_i-lk userbtn_i-act userbtn_i-ep">
            <a class="userbtn_lk" href=""><span class="undrl">Войти</span></a>
        </li>

        <li class="userbtn_i userbtn_i-act">
            <span class="userbtn_lk">
                <i class="userbtn_icon i-header i-header-compare"></i>
                <span class="userbtn_tx">Сравнение</span>
                <span class="userbtn_count">1</span>
            </span>
        </li>

        <li class="userbtn_i userbtn_i-act userbtn_i-cart">
            <a class="userbtn_lk userbtn_lk-cart" href="">
                <i class="userbtn_icon i-header i-header-cart"></i>
                <span class="userbtn_tx">Корзина</span>
                <span class="userbtn_count">2</span>
            </a>
        </li>
    </menu>
</div>
