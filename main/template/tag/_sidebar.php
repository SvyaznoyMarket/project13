<?php
/**
 * @var $page             \View\Layout
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $selectedCategory \Model\Product\Category\Entity
 * @var $limit            int
 */
?>

<dl class="bCtg">
    <dd>
        <ul>
            <? $i = 0; $count = count($categories); foreach ($categories as $category): $i++ ?>
            <? $selected = $selectedCategory->getId() == $category->getId() ?>
            <li class="bCtg__eL2<? if ($selected) echo ' mSelected' ?><?php if (!$selected && ($i > $limit)) echo ' hf' ?>">
                <a href="<?= $page->url('tag.category', ['tagToken' => $tag->getToken(), 'categoryToken' => $category->getToken()]) ?>"><span><?= $category->getName() ?> <b><?= $category->getProductCount() ?></b></span></a>
            </li>
            <? endforeach ?>
        </ul>
    </dd>

    <?php if (count($categories) > $limit): ?>
    <div class="bCtg__eMore"><a href="#">еще...</a></div>
    <?php endif ?>
</dl>

<? if(!empty($sidebarHotlinks)): ?>
    <?= $page->render('_hotlinks', array('hotlinks' => $hotlinks)) ?>
<? endif ?>
