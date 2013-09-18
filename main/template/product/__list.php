<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct
) {
    $data = (new \View\Product\ListAction())->execute(
        $helper,
        $pager,
        $productVideosByProduct
    );
?>

    <?= $helper->renderWithMustache('product/list/_compact', $data) ?>

    <script type="text/html">
        <?= include __DIR__ . '/list/_compact.mustache' ?>
    </script>

<? };