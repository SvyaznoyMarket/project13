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
    if ($count = count($category->getAncestor())) {
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
<div class="fixedTopBar__up">
    <a class="btnGrey fixedTopBar__upLink <?= $fixedBtn['class'] ?>" href="<?= $fixedBtn['link'] ?>" title="<?= $fixedBtn['title'] ?>">
        <? if ($fixedBtn['showCorner']): ?><em class="cornerTop">&#9650;</em><? endif; ?>
        <?= $fixedBtn['name'] ?>
    </a>
</div>
<? } ?>

<div class="fixedTopBar__crumbs">
    <? if (!empty($categoryImg)): ?>
        <a class="fixedTopBar__crumbsImg" href="#">
            <img class="crumbsImg" src="<?= $categoryImg ?>" alt="" />
        </a>
    <? endif; ?>
    <div class="wrapperCrumbsList"><?= $helper->render('__breadcrumbsUserbar', ['links' => $links]) ?></div>
</div>