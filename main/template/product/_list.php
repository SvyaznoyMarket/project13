<?php
/**
 * @var $page                   \View\Layout|\Templating\PhpEngine
 * @var $pager                  \Iterator\EntityPager
 * @var $view                   string
 * @var $isAjax                 bool
 * @var $productVideosByProduct array
 **/
?>

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