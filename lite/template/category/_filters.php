<?
/**
 * @var \Model\Product\Category\Entity|null $category
 * @var \Model\Product\Filter $productFilter
 * @var string $baseUrl
 * @var bool $openFilter
 */
?>
<? if ($category && $category->isV3()): ?>
    <?= \App::helper()->render('category/_filters.v3', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl]) ?>
<? elseif ($category && $category->isV2()): ?>
    <?= \App::helper()->render('category/_filters.v2', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl]) ?>
<? else: ?>
    <?= \App::helper()->render('category/_filters.v1', ['productFilter' => $productFilter, 'openFilter' => $openFilter, 'baseUrl' => $baseUrl, 'categories' => $categories]) ?>
<? endif ?>
