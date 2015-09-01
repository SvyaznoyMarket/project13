<?
/**
 * @var \Model\Product\Category\Entity|null $category
 * @var \Model\Product\Filter $productFilter
 * @var string $baseUrl
 * @var bool $openFilter
 */
?>
<? if ($category && $category->isV3()): ?>
    <?= \App::helper()->render('category/filters/v3/_filters', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl]) ?>
<? elseif ($category && $category->isV2()): ?>
    <?= \App::helper()->render('category/filters/v2/_filters', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl]) ?>
<? else: ?>
    <?= \App::helper()->render('category/filters/v1/_filters', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl, 'categories' => $categories]) ?>
<? endif ?>
