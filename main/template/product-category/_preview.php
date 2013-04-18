<?php
/**
 * @var $page         \View\Layout
 * @var $category     \Model\Product\Category\Entity
 * @var $rootCategory \Model\Product\Category\Entity
 */
?>

<?php
$productCount = $category->getProductCount() ?: $category->getGlobalProductCount();

// total text
$totalText = $productCount . ' ' . ($category->getHasLine()
    ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
    : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
);

$link = $category->getLink() . (\App::request()->get('instore') ? '?instore=1' : '');
?>

<div class="goodsbox mCatalog">
    <div class="goodsbox__inner">
    	<div class="photo">
	        <a href="<?= $link ?>">
	            <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
	        </a>
	    </div>
	    <h2><a href="<?= $link ?>" class="underline"><?= $category->getName() ?></a></h2>
		<div class="font11">
	        <a href="<?= $link ?>" class="underline gray"><?= $totalText ?></a>
	    </div>
    </div>
</div>
