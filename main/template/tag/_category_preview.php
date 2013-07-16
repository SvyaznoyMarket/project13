<?php
/**
 * @var $page         \View\Layout
 * @var $category     \Model\Product\Category\Entity
 * @var $rootCategory \Model\Product\Category\Entity
 */
?>

<?php
// total text
$productCount = $categoryProductCountsByToken[$category->getToken()];
$totalText = $productCount . ' ' . ($category->getHasLine()
    ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
    : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
);

$link = $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $category->getToken()]);
$token = $category->getToken();
$showImage = !empty($catalogJsonBulk[$token]) && !empty($catalogJsonBulk[$token]['logo_path']) && !empty($catalogJsonBulk[$token]['use_logo']);
?>

<div class="goodsbox mCatalog">
    <div class="goodsbox__inner">
      <div class="photo">
          <a href="<?= $link ?>">
              <img src="<?= $category->getImageUrl() ?>" alt="<?= $category->getName() . (empty($rootCategory) ? '' : ' - ' . $rootCategory->getName()) ?>" title="<?= $category->getName() . (empty($rootCategory) ? '' : ' - ' . $rootCategory->getName()) ?>" width="160" height="160"/>
          </a>
      </div>
      <h2><a href="<?= $link ?>" class="underline">
        <? if($showImage) { ?>
            <img src="<?= $catalogJsonBulk[$token]['logo_path'] ?>">
        <? } else { ?>
            <?= $category->getName() ?>
        <? } ?>
      </a></h2>
    <div class="font11">
          <a href="<?= $link ?>" class="underline gray"><?= $totalText ?></a>
      </div>
    </div>
</div>
