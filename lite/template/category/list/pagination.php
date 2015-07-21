<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $pager                 \Iterator\EntityPager
 */
$helper = \App::helper();

echo $helper->renderWithMustache('category/list/pagination', (new \View\PaginationAction())->execute($helper, $pager))

?>


