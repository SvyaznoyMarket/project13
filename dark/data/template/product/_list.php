<?php
/**
 * @var $page \View\DefaultLayout|\Templating\PhpEngine
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
            echo $page->render('product/list/_compact', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        case 'expanded':
            echo $page->render('product/list/_expanded', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        case 'line':
            echo $page->render('product/list/_line', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
        default:
            echo $page->render('product/list/_compact', array('pager' => $pager, 'isAjax' => $isAjax));
            break;
    } ?>

<? endif ?>