<?php
/**
 * @var $page        \Terminal\View\Search\IndexPage
 * @var $searchQuery string
 */
?>

<article id="categoryData" data-url="<?= $page->url('search.product', ['searchQuery' => $searchQuery]) ?>" class="bListing bContent mLoading" data-pagetype='product_list'>
    <div id="productList"></div>
    <div class="bProductListWrap mSizeLittle clearfix"></div>
</article>