<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) { ?>

    <?= $helper->renderWithMustache('product-category/v2/pagination', (new \View\PaginationAction())->execute(
        $helper,
        $pager,
        $category
    )) ?>

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/pagination.mustache') ?>
    </script>

<? };