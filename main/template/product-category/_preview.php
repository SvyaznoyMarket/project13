<?php
/**
 * @var $page         \View\Layout
 * @var $category     \Model\Product\Category\Entity
 * @var $rootCategory \Model\Product\Category\Entity
 */
?>

<?php
$productCount = $category->getProductCount();

// total text
$totalText = $productCount . ' ' . ($page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров')));

$link = $category->getLink() . (\App::request()->get('instore') ? '?instore=1' : '');
?>

<div class="goodsbox mCatalog js-goodsbox">
    <div class="goodsbox__inner js-goodsboxContainer">
    	<div class="photo">
	        <a href="<?= $link ?>">
	            <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
	        </a>
	    </div>
	    <div class="h2">
            <a href="<?= $link ?>" class="underline">
                <?= $category->getName() ?>
            </a>
        </div>
		<div class="font11">
	        <a href="<?= $link ?>" class="underline gray"><?= $totalText ?></a>
	    </div>
    </div>
</div>
