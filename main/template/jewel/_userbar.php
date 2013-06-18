<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Lightbox -->
<div class="lightbox">
    <div class="lightboxinner">
        <div class="dropbox" style="left:733px; display:none;">
            <p>Перетащите сюда</p>
        </div>
        <!-- Flybox -->
        <ul class="lightboxmenu">
            <li class="fl">
                <a href="<?= $page->url('user.login') ?>" class="point point1"><b></b>Личный кабинет</a>
            </li>
            <li>
                <a href="<?=  $page->url('cart') ?>" class="point point2"><b></b>Моя корзина<span class="total" style="display:none;">
                    <span id="sum"></span> &nbsp;<span class="rubl">p</span></span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!-- /Lightbox -->