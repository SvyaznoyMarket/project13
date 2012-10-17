<?php
/**
 * @var $page          \View\DefaultLayout
 * @var $productFilter \Model\Product\Filter
 */
?>

<?php
$formFilter = new \View\Product\FilterForm($productFilter);
$list = $formFilter->getSelected();
?>

<? if ((bool)$list): ?>
    <dd class="bSpecSel">
        <h3>Ваш выбор:</h3>
        <ul>
            <? foreach ($list as $item): ?>
            <li>
                <a href="<?= $item['url'] ?>" title="<?= $item['title'] ?>"><b>x</b> <?= $item['name'] ?><?= ('price' == $item['type'] ? '&nbsp;<span class="rubl">p</span>' : '') ?></a>
            </li>
            <? endforeach ?>
        </ul>
        <a class="bSpecSel__eReset" href="<?= $page->url('product.category', array('categoryPath' => $productFilter->getCategory()->getPath())) ?>">сбросить все</a>
    </dd>
<? endif ?>