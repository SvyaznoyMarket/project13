<?php
/**
 * @var $page       \View\Layout
 * @var $category   \Model\Product\Category\Entity|null
 * @var $slice      \Model\Slice\Entity|null
 * @var $fixedBtn   array
 */
if (!isset($slice)) $slice = null;
$helper = new \Helper\TemplateHelper();
$links = [];
$categoryImg = false;
$i = 0;

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
    $categoryImg = $category->getImageUrl();
}

if (0 == $i && $slice) {
    $links[] = ['name' => $slice->getTitle(), 'url' => null, 'last' => true];
}

?>
<? if (isset($fixedBtn['name'])) { ?>
<div class="topbarfix_up">
    <a class="btnGrey topbarfix_upLink <?= $fixedBtn['class'] ?> js-userbar-upLink" href="<?= $fixedBtn['link'] ?>" title="<?= $fixedBtn['title'] ?>">
        <? if ($fixedBtn['showCorner']): ?><em class="cornerTop">&#9650;</em><? endif; ?>
        <?= $fixedBtn['name'] ?>
    </a>
</div>
<? } ?>

<div class="userbar-crumbs">
    <? if (!empty($categoryImg)): ?>
        <a class="userbar-crumbs-img" href="#">
            <img class="userbar-crumbs-img__img" src="<?= $categoryImg ?>" alt="" />
        </a>
    <? endif; ?>
    <div class="userbar-crumbs-wrap"><?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?></div>
</div>