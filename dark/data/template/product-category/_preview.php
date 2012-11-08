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
if ($category->getHasLine()) {
    $totalText = $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% серий|{n: n % 10 == 1}%count% серия|{n: n % 10 > 1 && n % 10 < 5}%count% серии|(1,+Inf]%count% серий', array('%count%' => $productCount), $productCount);
} else {
    $totalText = $page->helper->formatNumberChoice('{n: n > 10 && n < 20}%count% товаров|{n: n % 10 == 1}%count% товар|{n: n % 10 > 1 && n % 10 < 5}%count% товара|(1,+Inf]%count% товаров', array('%count%' => $productCount), $productCount);
}
?>

<div class="goodsbox height250">
    <div class="goodsbox__inner">
    	<div class="photo">
	        <a href="<?= $category->getLink() ?>">
	            <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
	        </a>
	    </div>
	    <h2><a href="<?= $category->getLink() ?>" class="underline"><?= $category->getName() ?></a></h2>
		<div class="font11">
	        <a href="<?= $category->getLink() ?>" class="underline gray"><?= $totalText ?></a>
	    </div>
    </div>
</div>
