<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting
) { ?>

    <?= $helper->renderWithMustache('product/_listAction-sorting', (new \View\Product\SortingAction())->execute(
        $helper,
        $productSorting
    )) ?>
    <script id="tplSorting" type="text/html" data-partial="{}">
        <?= file_get_contents(\App::config()->templateDir . '/product/_listAction-sorting.mustache') ?>
    </script>

<? };
