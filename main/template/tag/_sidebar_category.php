<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 * @var $tag      \Model\Tag\Entity
 */
?>

<? if($category->isRoot()) { ?>
  <dl class="bCtg" style="border-bottom:0;">
    <dd>
      <ul>
        <? foreach (array_keys($sidebarCategoriesTree) as $rootToken) { ?>
          <li class="bCtg__eL1 mBold">
            <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $rootToken]); ?>">
              <span><?= $categoriesByToken[$rootToken]->getName() ?></span>
            </a>
          </li>
          <? foreach (array_keys($sidebarCategoriesTree[$rootToken]) as $parentToken) { ?>
            <li class="bCtg__eL3">
              <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $parentToken]); ?>">
                <span><?= $categoriesByToken[$parentToken]->getName() ?> <span class="gray"><?= $categoryProductCountsByToken[$parentToken] ?></span></span>
              </a>
            </li>
          <? } ?>
        <? } ?>
      </ul>
    </dd>
  </dl>
<? } ?>
