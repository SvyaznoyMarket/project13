<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting
) { ?>

<?= $helper->renderWithMustache('product/_listAction-sorting', (new \View\Product\SortingAction())->execute(
    $helper,
    $productSorting
)) ?>

<? };
