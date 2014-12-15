<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting
) { ?>

    <?= $helper->renderWithMustache('gift/category/_sorting', (new \View\Product\SortingAction())->execute(
        $helper,
        $productSorting
    )) ?>

    <script class="tplSorting" type="text/html" data-partial="{}">
        <?= file_get_contents(\App::config()->templateDir . '/gift/category/_sorting.mustache') ?>
    </script>

<? };
