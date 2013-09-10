<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category
) { ?>

    <!-- Категории товаров -->
    <ul class="bCatalogList clearfix">
    <? foreach ($category->getChild() as $child): ?>
        <li class="bCatalogList__eItem">
            <a class="bCatalogList__eLink" href="">
				<span class="bCategoriesImg">
					<img class="bCategoriesImg__eImg" src="<?= $child->getImageUrl() ?>" alt="<?= $helper->escape($child->getName())?>" />
				</span>

                <span class="bCategoriesName"><?= $child->getName() ?></span>
            </a>
        </li>
    <? endforeach ?>
    </ul>
    <!-- /Категории товаров -->

<? };