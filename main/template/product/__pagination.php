<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

    <?= $helper->renderWithMustache('pagination', (new \View\PaginationAction())->execute(
        $helper,
        $pager
    )) ?>

    <script class="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/pagination.mustache') ?>
    </script>

<? };