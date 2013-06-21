<?php
/**
 * @var $page                   \View\Layout|\Templating\PhpEngine
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool
 **/
?>

<?
if (!isset($isAjax)) $isAjax = false;
if (!isset($isAddInfo)) $isAddInfo = false;
?>

<? if (!$pager->count()) { ?>
    <div class="clear"></div>
    <div style="margin:0 auto;width:260px;padding: 160px 0;">Нет товаров с такими характеристиками</div>
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