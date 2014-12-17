<?php
/**
 * @var $page \View\Layout
 * @var $category \Model\Product\Category\Entity|null
 * @var $productFilter \Model\Product\Filter|null
 * @var $v2 bool
 */
$helper = new \Helper\TemplateHelper();
$links = [];

if ($category) {
    if ($count = count($category->getAncestor())) {
        $i = 1;
        foreach ($category->getAncestor() as $ancestor) {
            $links[] = ['name' => $ancestor->getName(), 'url'  => $ancestor->getLink(), 'last' => $i == $count];
            $i++;
        }
    } else {
        $links[] = ['name' => $category->getName(), 'url'  => $category->getLink() ? $category->getLink() : null, 'last' => true];
    }
} ?>

<div class="topbarfix_up">
    <? if (isset($productFilter) && $productFilter->hasAlwaysShowFilters() || $category && $category->isV2() || isset($v2) && $v2): ?>
        <a class="topbarfix_upLink topbarfix_upLink-custom js-userbar-upLink" href="">
            <em class="cornerTop">&#9650;</em> <span class="topbarfix_upLink_tx">Параметры</span>
        </a>
    <? else: ?>
        <a class="btnGrey topbarfix_upLink js-userbar-upLink" href="">
            <em class="cornerTop">&#9650;</em> Бренды и параметры
        </a>
    <? endif ?>
</div>

<div class="topbarfix_crumbs">
    <a class="topbarfix_crumbsImg" href="#"><img class="crumbsImg" src="<?= $category ? $category->getImageUrl() : '' ?>" alt="" /></a>
    <div class="topbarfix_wrapCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>