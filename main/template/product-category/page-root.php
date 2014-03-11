<?php
/**
 * @var $page     \View\ProductCategory\RootPage
 * @var $category \Model\Product\Category\Entity
 * @var $relatedCategories      array
 */

$categories = $category->getChild();
if (!empty($relatedCategories)) $categories = array_merge($categories, $relatedCategories);
?>

<h1 class="bTitlePage"><?= $category->getName() ?></h1>

<!-- Баннер --><div id="adfox683" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->

<? if (count($categories)): ?>
    <ul class="bCatalogRoot clearfix">
        <? /*
        <!--li class="bCatalogRoot__eItem mBannerItem" style="width: 0px;"><-div class="adfoxWrapper" id="adfox215"></div></li-->
        <!-- место для баннеры 460х260, при этом родительский элемент имеет ширину 240 -->
        */ ?>
        <? $j = 0; ?>
        <? foreach ($categories as $child): ?>
            <?php
            $productCount = $child->getProductCount() ? : $child->getGlobalProductCount();

            $totalText = '';
            if ( $productCount > 0 ) {
                $totalText = $productCount . ' ' . ($child->getHasLine()
                        ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
                        : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
                    );
            }


            $link = $child->getLink() . (\App::request()->get('instore') ? '?instore=1' : '');

            ?>

            <li class="bCatalogRoot__eItem">
                <a class="bCatalogRoot__eItemLink"
                   href="<?= $link ?>"
                   title="<?= $child->getName() ?> - <?= $category->getName() ?>">

                    <div class="bCatalogRoot__eImgLink">
                        <img class="bCatalogRoot__eImg"
                         src="<?= $child->getImageUrl() ?>"
                         alt="<?= $child->getName() ?> - <?= $category->getName() ?>"/>
                    </div>

                    <div class="bCatalogRoot__eNameLink">
                        <?= $child->getName() ?>
                    </div>

                    <div class="bCatalogRoot__eCount"><?= $totalText ?></div>
                </a>
            </li>

        <? endforeach ?>
    </ul>
    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
<? endif; ?>