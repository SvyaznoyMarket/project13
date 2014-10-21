<?php
/**
 * @var $page                   \View\Layout|\Templating\PhpEngine
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 * @var $isAddInfo              bool
 * @var $itemsPerRow            int
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
        case 'compact_with_bottom_description':
            print $page->render('jewel/product/list/_compact', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'view' => [
                    'descriptionPosition' => 'bottom',
                    'descriptionHover' => false,
                ],
            ]);
            break;
        case 'compact_with_hover_bottom_description':
            print $page->render('jewel/product/list/_compact', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'view' => [
                    'descriptionPosition' => 'bottom',
                    'descriptionHover' => true,
                ],
            ]);
            break;
        case 'compact_without_description':
            print $page->render('jewel/product/list/_compact', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'view' => [
                    'descriptionPosition' => 'none',
                    'descriptionHover' => false,
                ],
            ]);
            break;
        case 'expanded':
            print $page->render('jewel/product/list/_expanded', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
            ]);
            break;
        case 'line':
            print $page->render('jewel/product/list/_line', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
            ]);
            break;
        default:
            print $page->render('jewel/product/list/_compact', [
                'pager' => $pager,
                'isAjax' => $isAjax,
                'productVideosByProduct' => $productVideosByProduct,
                'isAddInfo' => $isAddInfo,
                'itemsPerRow' => $itemsPerRow,
                'view' => [
                    'descriptionPosition' => 'top',
                    'descriptionHover' => false,
                ],
            ]);
            break;
    } ?>

<? } ?>