<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category
) { ?>
    <!-- Хлебные крохи -->
    <ul class="bBreadcrumbs clearfix">
    <? $i = 1; $count = count($category->getAncestor()); foreach ($category->getAncestor() as $ancestor): ?>
        <li class="bBreadcrumbs__eItem<? if ($i == $count): ?> mLast<? endif ?>">
            <a class="bBreadcrumbs__eLink" href="<?= $helper->url('product.category', ['categoryPath' => $category->getPath()]) ?>"><?= $ancestor->getName() ?></a>
        </li>
    <? $i++; endforeach ?>
    </ul>
    <!-- /Хлебные крохи -->
<? };