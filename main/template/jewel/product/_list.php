<?php
/**
 * @var $page                   \View\Layout|\Templating\PhpEngine
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $isAjax                 bool
 * @var $isAddInfo              bool
 * @var $itemsPerRow            int
 **/
?>

<?
if (!isset($isAjax)) $isAjax = false;
if (!isset($isAddInfo)) $isAddInfo = false;
if (!isset($category)) $category = null;
?>

<? if (!$pager->count()) { ?>
    <div class="clear"></div>
    <div style="margin:0 auto;width:260px;padding: 160px 0;">Нет товаров с такими характеристиками</div>
<? } else { ?>
    <?php switch ($view) {
        case 'expanded':
            print $page->render('jewel/product/list/_expanded', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'category' => $category,
            ]);
            break;
        default:
            print $page->render('jewel/product/list/_compact', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'category' => $category,
            ]);
            break;
    } ?>

<? } ?>