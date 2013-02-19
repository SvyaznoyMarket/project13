<?php
/**
 * @var $page                   \View\Layout|\Templating\PhpEngine
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 **/
?>

<style type="text/css">
    .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small, .goodsphoto_eVideoShield.goodsphoto_eVideoShield_small:hover {
        background: url('/css/item/img/videoStiker_small.png') no-repeat 0 0;
        right: -55px;
        top: 130px;
    }
</style>

<?
if (!isset($isAjax)) $isAjax = false;
?>

<? if (!$pager->count()) { ?>
    <div class="clear"></div>
    <p>нет товаров</p>

<? } else { ?>
    <?php switch ($view) {
        case 'compact':
            require __DIR__ . '/list/_compact.php';
            break;
        case 'expanded':
            require __DIR__ . '/list/_expanded.php';
            break;
        case 'line':
            require __DIR__ . '/list/_line.php';
            break;
        default:
            require __DIR__ . '/list/_compact.php';
            break;
    } ?>

<? } ?>