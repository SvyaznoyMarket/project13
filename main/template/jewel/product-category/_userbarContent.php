<?php
/**
 * @var $page \View\Layout
 * @var $category \Model\Product\Category\Entity|null
 */
$helper = new \Helper\TemplateHelper();
$links = [];

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

<div class="topbarfix_crumbs">
    <a class="topbarfix_crumbsImg" href=""><img class="crumbsImg" src="<?= $category ? $category->getImageUrl() : '' ?>" /></a>
    <div class="topbarfix_wrapCrumbsList">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>