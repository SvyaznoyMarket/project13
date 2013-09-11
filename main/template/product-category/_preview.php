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
$token = $category->getToken();
$showImage = !empty($catalogJsonBulk[$token]) && !empty($catalogJsonBulk[$token]['logo_path']) && !empty($catalogJsonBulk[$token]['use_logo']);
$addInfo = isset($addInfo)?$addInfo:[];

?>

<div class="goodsbox mCatalog">
    <div class="goodsbox__inner" <?= (count($addInfo)) ? 'data-add="' . $page->json($addInfo) . '"' : ''; ?>>
    	<div class="photo">
	        <a href="<?= $link ?>" class="kiss_cat_clicked">
	            <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" title="<?= $category->getName() ?> - <?= $rootCategory->getName() ?>" width="160" height="160"/>
	        </a>
	    </div>
	    <div class="h2">
            <a href="<?= $link ?>" class="underline kiss_cat_clicked">
                <? if ($showImage) { ?>
                    <img src="<?= $catalogJsonBulk[$token]['logo_path'] ?>">
                <? } else { ?>
                    <?= $category->getName() ?>
                <? } ?>
            </a>
        </div>
		<div class="font11">
	        <a href="<?= $link ?>" class="underline gray kiss_cat_clicked"><?= $totalText ?></a>
	    </div>
    </div>
</div>
