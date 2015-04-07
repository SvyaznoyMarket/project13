<?php
/**
 * @var $page \View\Layout
 * @var $category \Model\Product\Category\Entity|null
 * @var $productFilter \Model\Product\Filter|null
 * @var $v2 bool|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];

if (!isset($category)) $category = null;

if ($category) {
    if ($category instanceof \Model\Product\Category\Entity && $count = count($category->getAncestor())) {
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
    <? if ($category && ($category->isV2() || $category->isV3()) || isset($v2) && $v2): ?>
        <a class="topbarfix_upLink topbarfix_upLink-custom js-userbar-upLink" href="">
            <em class="cornerTop">&#9650;</em> <span class="topbarfix_upLink_tx">Параметры</span>
        </a>
    <? else: ?>
        <a class="btnGrey topbarfix_upLink js-userbar-upLink" href="">
            <em class="cornerTop">&#9650;</em> Бренды и параметры
        </a>
    <? endif ?>
</div>

<div class="userbar-crumbs">
    <a class="userbar-crumbs-img" href="#"><img class="userbar-crumbs-img__img" src="<?= $category ? $category->getImageUrl() : '' ?>" alt="" /></a>
    <div class="userbar-crumbs-wrap">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>