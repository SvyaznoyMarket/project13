<?
/**
 * @var $page                   \View\ProductCategory\LeafPage
 * @var $productPager           \Iterator\EntityPager
 */

$helper = \App::helper();

foreach ($productPager as $product) {
    if (!$product instanceof \Model\Product\Entity) continue;
    $data = (new \View\Product\ShowAction())->execute($helper, $product, null, null, null, new \View\Product\ReviewCompactAction());

    // подготовим звезды к другому темплейту
    if (isset($data['review']) && isset($data['review']['stars'])) {
        foreach ($data['review']['stars'] as &$item) {
            $item['show'] = $item['image'] == '/images/reviews_star.png';
        }
    }

    echo $helper->renderWithMustache('category/list/pager', $data);
}

?>
