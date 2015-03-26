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
    <h1 class="bTitlePage js-pageTitle"><?= $category->getName() ?></h1>

    <!-- Баннер --><div id="adfox683" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->

<? if ('furniture' === $category_class): ?>
    <?= $helper->renderWithMustache('furniture/product-category/_listInFilter', [
        'links' => $links,
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>
<? elseif (count($links)): ?>
    <? if ($category->isV2Root()): ?>
        <?= $helper->renderWithMustache('product-category/v2/root/_brands', (new View\Partial\ProductCategory\RootPage\Brands)->execute($productFilter)) ?>
    <? endif ?>

    <div class="js-category-v2-root-linksWrapper">
        <?= $helper->renderWithMustache('product-category/root/_links', (new View\Partial\ProductCategory\RootPage\Links)->execute($links, $category)) ?>
    </div>

    <script id="root_page_links_tmpl" type="text/html" data-partial="{}">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/root/_links.mustache') ?>
    </script>

    <? if ($category->isV2Root()): ?>
        <script id="root_page_selected_brands_tmpl" type="text/html" data-partial="{}">
            <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/root/_selectedBrands.mustache') ?>
        </script>
    <? endif ?>

    <div style="margin: 0 0 30px;">
        <? if (\App::config()->product['pullRecommendation'] && \App::config()->product['viewedEnabled']): ?>
            <?= $helper->render('product/__slider', [
                'type'      => 'viewed',
                'title'     => 'Вы смотрели',
                'products'  => [],
                'count'     => null,
                'limit'     => \App::config()->product['itemsInSlider'],
                'page'      => 1,
                'url'       => $page->url('product.recommended'),
                'sender'    => [
                    'name'     => 'enter',
                    'position' => 'Viewed',
                    'from'     => 'categoryPage'
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