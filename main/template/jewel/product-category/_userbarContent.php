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

<div class="userbar-crumbs">
    <a class="userbar-crumbs-img" href=""><img class="userbar-crumbs-img__img" src="<?= $category ? $category->getImageUrl() : '' ?>" /></a>
    <div class="userbar-crumbs-wrap">
        <?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?>
    </div>
</div>