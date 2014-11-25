<?php
/**
 * @var $page                   \View\ProductCategory\RootPage
 * @var $category               \Model\Product\Category\Entity
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 * @var $links                  array
 * @var $productFilter          \Model\Product\Filter
 */

$helper = new \Helper\TemplateHelper();
$category_class = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;
?>
<h1 class="bTitlePage"><?= $category->getName() ?></h1>

<!-- Баннер --><div id="adfox683" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->

<? if ('furniture' === $category_class): ?>
    <?= $helper->renderWithMustache('furniture/product-category/_listInFilter', [
        'links' => $links,
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>
<? elseif (count($links)): ?>
    <? if ($category->isAppliancesRoot()): ?>
        <?= $helper->renderWithMustache('product-category/rootPage/_brands', (new View\Partial\ProductCategory\RootPage\Brands)->execute($productFilter)) ?>
    <? endif ?>

    <div class="js-productCategory-rootPage-linksWrapper">
        <?= $helper->renderWithMustache('product-category/rootPage/_links', (new View\Partial\ProductCategory\RootPage\Links)->execute($links, $category)) ?>
    </div>

    <script id="root_page_links_tmpl" type="text/html" data-partial="{}">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/rootPage/_links.mustache') ?>
    </script>

    <script id="root_page_selected_brands_tmpl" type="text/html" data-partial="{}">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/rootPage/_selectedBrands.mustache') ?>
    </script>

    <div class="margin: 0 0 30px;">
        <? if (\App::config()->product['pullRecommendation'] && !$isTchibo): ?>
            <?= $helper->render('product/__slider', [
                'type'      => 'viewed',
                'title'     => 'Вы смотрели',
                'products'  => [],
                'count'     => null,
                'limit'     => \App::config()->product['itemsInSlider'],
                'page'      => 1,
                'url'       => $page->url('product.recommended'),
                'sender'    => [
                    'name'     => 'retailrocket',
                    'position' => 'Viewed',
                ],
            ]) ?>
        <? endif ?>
    </div>

    <? if(!empty($seoContent)): ?>
        <div class="bSeoText">
            <?= $seoContent ?>
        </div>
    <? endif ?>
<? endif ?>