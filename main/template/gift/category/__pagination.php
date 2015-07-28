<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

    <!--noindex-->
    <?= $helper->renderWithMustache('gift/category/pagination', (new \View\PaginationAction())->execute(
        $helper,
        $pager
    )) ?>
    <!--/noindex-->

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/gift/category/pagination.mustache') ?>
    </script>

<? };