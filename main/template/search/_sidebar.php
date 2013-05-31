<?php
/**
 * @var $page             \View\Layout
 * @var $searchQuery      string
 * @var $categories       \Model\Product\Category\Entity[]
 * @var $selectedCategory \Model\Product\Category\Entity|null
 * @var $limit            int
 */
?>

<?
$selectedId = $selectedCategory ? $selectedCategory->getId() : null;
?>


<dl class="bCtg">
    <dt class="mBold"></dt>
    <dd>
        <ul>
            <li class="mBold<? if (null === $selectedId) echo ' mSelected' ?>">
                <a href="<?= $page->url('search', array('q' => $searchQuery)) ?>"><span>Все результаты</span></a>
            </li>
        </ul>
    </dd>

    <dt class="mBold">Товары по категориям</dt>
    <dd>
        <ul>
            <? $i = 0; $count = count($categories); foreach ($categories as $category): $i++ ?>
                <? $selected = $selectedId == $category->getId() ?>
                <li class="bCtg__eL2<? if ($selected) echo ' mSelected' ?><?php if (!$selected && ($i > $limit)) echo ' hf' ?>">
                    <a href="<?= $page->url('search', array('q' => $searchQuery, 'category' => $category->getId())) ?>"><span><?= $category->getName() ?> <b><?= $category->getProductCount() ?></b></span></a>
                </li>
            <? endforeach ?>
        </ul>
    </dd>

    <?php if (count($categories) > $limit): ?>
        <div class="bCtg__eMore"><a href="#">еще...</a></div>
    <?php endif ?>
</dl>
