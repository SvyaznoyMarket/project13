<?php
/**
 * @var $page                   \View\ProductCategory\RootPage
 * @var $category               \Model\Product\Category\Entity
 * @var $relatedCategories      array
 * @var $categoryConfigById     array
 */

$helper = new \Helper\TemplateHelper();
$category_class = !empty($catalogJson['category_class']) ? strtolower(trim((string)$catalogJson['category_class'])) : null;

$links = [];
$categories = $category->getChild();
if (!empty($relatedCategories)) $categories = array_merge($categories, $relatedCategories);

foreach ($categories as $child) {
    /** @var $child \Model\Product\Category\Entity */

    $config = isset($categoryConfigById[$child->getId()]) ? $categoryConfigById[$child->getId()] : null;
    $productCount = $child->getProductCount() ? : $child->getGlobalProductCount();
    $totalText = '';

    if ( $productCount > 0 ) {
        $totalText = $productCount . ' ' . ($child->getHasLine()
                ? $page->helper->numberChoice($productCount, array('серия', 'серии', 'серий'))
                : $page->helper->numberChoice($productCount, array('товар', 'товара', 'товаров'))
            );
    }

    $image_size = 'furniture' === $category_class ? 3 : 0;

    $links[] = [
        'name'          => isset($config['name']) ? $config['name'] : $child->getName(),
        //'url'           => $child->getLink(),
        'url'           => $child->getLink() . (\App::request()->get('instore') ? '?instore=1' : ''),
        'image'         => (is_array($config) && array_key_exists('image', $config)) ? $config['image'] : $child->getImageUrl($image_size),
        //'active'        => false, // пока тут не используется
        'css'           => isset($config['css']) ? $config['css'] : null,
        'totalText'     => $totalText,
    ];
}

?>
<h1 class="bTitlePage"><?= $category->getName() ?></h1>

<!-- Баннер --><div id="adfox683" class="adfoxWrapper bBannerBox"></div><!--/ Баннер -->

<? if ('furniture' === $category_class): ?>
    <?= $helper->renderWithMustache('furniture/product-category/_listInFilter', [
        'links' => $links,
        'promoStyle' => !empty($promoStyle) ? $promoStyle : '',
    ]) ?>
<? elseif (count($links)): ?>
    <ul class="bCatalogRoot clearfix">
        <? /*
        <!--li class="bCatalogRoot__eItem mBannerItem" style="width: 0px;"><-div class="adfoxWrapper" id="adfox215"></div></li-->
        <!-- место для баннеры 460х260, при этом родительский элемент имеет ширину 240 -->
        */ ?>
        <? $j = 0; ?>
        <? foreach ($links as $child): ?>
            <li class="bCatalogRoot__eItem">
                <a class="bCatalogRoot__eItemLink"
                   href="<?= $child['url'] ?>"<?
                   if(isset($child['css']['link'])): ?> style="<?= $child['css']['link'] ?>"<? endif
                    ?> title="<?= $child['name'] ?> - <?= $category->getName() ?>">

                    <div class="bCatalogRoot__eImgLink">
                        <img class="bCatalogRoot__eImg"
                         src="<?= $child['image'] ?>"
                         alt="<?= $child['name'] ?> - <?= $category->getName() ?>"/>
                    </div>

                    <div class="bCatalogRoot__eNameLink">
                        <?= $child['name'] ?>
                    </div>

                    <div class="bCatalogRoot__eCount"<?
                        if(isset($child['css']['name'])): ?> style="<?= $child['css']['name'] ?>"<? endif
                        ?>>
                        <?= $child['totalText'] ?>
                    </div>
                </a>
            </li>
        <? endforeach ?>
    </ul>
    
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