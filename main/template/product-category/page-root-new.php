<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 */

?>

<h1 class="bTitlePage"><?= $category->getName() ?></h1>

<!-- Баннер --><div class="bBannerBox"></div><!--/ Баннер -->

<? if (count($category->getChild())): ?>
    <ul class="bCatalogRoot clearfix">
        <li class="bCatalogRoot__eItem mBanner2Item" style="width: 0px;"></li>
        <!-- место для баннеры 460х260, при этом родительский элемент имеет ширину 480 -->
        <? $j = 0; ?>
        <? foreach ($category->getChild() as $child): ?>
            <?php
            $productCount = $child->getProductCount() ? : $child->getGlobalProductCount();

            $totalText = $productCount . ' ' . ($child->getHasLine()
                ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
                : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
            );

            $link = $child->getLink() . (\App::request()->get('instore') ? '?instore=1' : '');

            ?>

            <li class="bCatalogRoot__eItem">
                <a class="bCatalogRoot__eImgLink"
                   href="<?= $link ?>"
                   title="<?= $child->getName() ?> - <?= $category->getName() ?>">
                    <img class="bCatalogRoot__eImg"
                         src="<?= $child->getImageUrl() ?>"
                         alt="<?= $child->getName() ?> - <?= $category->getName() ?>"/>
                </a>

                <a class="bCatalogRoot__eNameLink"
                   href="<?= $link ?>"
                   title="<?= $child->getName() ?> - <?= $category->getName() ?>">
                    <?= $child->getName() ?>
                </a>

                <div class="bCatalogRoot__eCount"><?= $totalText ?></div>
            </li>

        <? endforeach ?>
    </ul>
<? endif; ?>