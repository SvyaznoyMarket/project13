<?php
/**
 * @var $page \View\DefaultLayout
 * @var $pager \Iterator\EntityPager
 * @var $view string
 * @var $isAjax bool
 * */
?>

<?php
if (!isset($isAjax)) $isAjax = false;
?>

<? if (!$pager->count()): ?>
<div class="clear"></div>
<p>нет товаров</p>

<? else: ?>
    <?php switch ($view) {
        case 'compact':
            $page->render('product/list/_compact', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        case 'expanded':
            $page->render('product/list/_expanded', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        case 'line':
            $page->render('product/list/_line', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        default:
            $page->render('product/list/_compact', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
    } ?>

<? endif ?>