<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) { ?>

    <?= $helper->renderWithMustache('_pagination', (new \View\PaginationAction())->execute(
        $helper,
        $pager
    )) ?>

    <script id="tplPagination" type="text/html" data-partial="">
        <?= file_get_contents(\App::config()->templateDir . '/_pagination.mustache') ?>
    </script>

<? };