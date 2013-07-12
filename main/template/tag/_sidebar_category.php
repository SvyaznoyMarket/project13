<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 * @var $tag      \Model\Tag\Entity
 */
?>

<? if(empty($category)) { ?>
  <dl class="bCtg" style="border-bottom:0;">
    <dd>
      <ul>
        <li class="bCtg__eL1 mBold">
          <a href="<?= $page->url('tag', ['tagToken' => $tag->getToken()]); ?>">
            <span><?= $tag->getName() ?></span>
          </a>
        </li>
        <? foreach (array_keys($sidebarCategoriesTree) as $rootToken) { ?>
          <li class="bCtg__eL2 mBold">
            <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $rootToken]); ?>">
              <span><?= $categoriesByToken[$rootToken]->getName() ?></span>
            </a>
          </li>
          <ul class="pb20">
            <? $count = 1 ?>
            <? foreach (array_keys($sidebarCategoriesTree[$rootToken]) as $parentToken) { ?>
              <li class="bCtg__eL3<?= $count > 5 ? ' hf more_item' : '' ?>">
                <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $parentToken]); ?>">
                  <span><?= $categoriesByToken[$parentToken]->getName() ?> <span class="gray"><?= $categoryProductCountsByToken[$parentToken] ?></span></span>
                </a>
              </li>
              <? $count++ ?>
            <? } ?>
            <? if($count > 5) { ?>
              <li class="bCtg__eL3 bCtg__eMore">
                <a href="#">еще...</a>
              </li>
            <? } ?>
          </ul>
        <? } ?>
      </ul>
    </dd>
  </dl>
<? } elseif($category->isRoot()) { ?>
  <dl class="bCtg" style="border-bottom:0;">
    <dd>
      <ul>
        <li class="bCtg__eL1 mBold">
          <a href="<?= $page->url('tag', ['tagToken' => $tag->getToken()]); ?>">
            <span><?= $tag->getName() ?></span>
          </a>
        </li>
        <li class="bCtg__eL2 mBold">
          <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $category->getToken()]); ?>">
            <span><?= $categoriesByToken[$category->getToken()]->getName() ?></span>
          </a>
        </li>
        <ul>
          <? foreach (array_keys($sidebarCategoriesTree[$category->getToken()]) as $parentToken) { ?>
            <li class="bCtg__eL3">
              <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $parentToken]); ?>">
                <span><?= $categoriesByToken[$parentToken]->getName() ?> <span class="gray"><?= $categoryProductCountsByToken[$parentToken] ?></span></span>
              </a>
            </li>
          <? } ?>
        </ul>
      </ul>
    </dd>
  </dl>
<? } else { ?>
  <dl class="bCtg" style="border-bottom:0;">
    <dd>
      <ul>
        <li class="bCtg__eL1 mBold">
          <a href="<?= $page->url('tag', ['tagToken' => $tag->getToken()]); ?>">
            <span><?= $tag->getName() ?></span>
          </a>
        </li>
        <li class="bCtg__eL2 mBold">
          <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $rootCategory->getToken()]); ?>">
            <span><?= $categoriesByToken[$rootCategory->getToken()]->getName() ?></span>
          </a>
        </li>
        <ul>
          <? foreach (array_keys($sidebarCategoriesTree[$rootCategory->getToken()]) as $parentToken) { ?>
            <li class="bCtg__eL3<?= $parentToken == $category->getToken() ? ' mSelected' : '' ?>">
              <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $parentToken]); ?>">
                <span><?= $categoriesByToken[$parentToken]->getName() ?> <span class="gray"><?= $categoryProductCountsByToken[$parentToken] ?></span></span>
              </a>
            </li>
          <? } ?>
        </ul>
      </ul>
    </dd>
  </dl>
<? } ?>
