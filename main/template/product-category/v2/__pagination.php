<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) { ?>

    <!--noindex-->
    <?= $helper->renderWithMustache('product-category/v2/pagination', (new \View\PaginationAction())->execute(
        $helper,
        $pager,
        $category
    )) ?>
    <!--/noindex-->

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/product-category/v2/pagination.mustache') ?>
    </script>

<? };