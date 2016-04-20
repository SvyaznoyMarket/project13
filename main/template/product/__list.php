<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param array $listViewData
 */
return function(
    \Helper\TemplateHelper $helper,
    $listViewData
) {
    $partials = [
        'cart/_button-product'        => '#listing_cart_button_product_tmpl',
        'cart/_button-product-lstn'   => '#listing_cart_button_product_lstn_tmpl',
        'product/_review-compact'     => '#listing_product_review_compact_tmpl',
        'product/_favoriteButton'     => '#listing_product_favorite_button_tmpl',
        'product/variations'          => '#listing_product_variations_tmpl',

        'product/list/_compact'       => '#listing_list_compact_tmpl',
        'product/list/_expanded'      => '#listing_list_expanded_tmpl',
        'product/list/_light'         => '#listing_list_light_tmpl',

        'product/list/item/compact'   => '#listing_item_compact_tmpl',
        'product/list/item/_expanded' => '#listing_item_expanded_tmpl',
        'product/list/item/light'     => '#listing_item_light_tmpl',
    ];
?>
    <?= $helper->renderWithMustache('product/list', $listViewData) ?>

    <script id="listing_list_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list.mustache') ?>
    </script>

    <script id="listing_list_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/_compact.mustache') ?>
    </script>

    <script id="listing_list_expanded_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/_expanded.mustache') ?>
    </script>

    <script id="listing_list_light_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/_light.mustache') ?>
    </script>

    <? /**/ ?>

    <script id="listing_item_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/item.mustache') ?>
    </script>

    <script id="listing_item_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/item/compact.mustache') ?>
    </script>

    <script id="listing_item_expanded_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/item/_expanded.mustache') ?>
    </script>

    <script id="listing_item_light_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/list/item/light.mustache') ?>
    </script>

    <? /**/ ?>

    <script id="listing_cart_button_product_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/cart/_button-product.mustache') ?>
    </script>

    <script id="listing_cart_button_product_lstn_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/cart/_button-product-lstn.mustache') ?>
    </script>

    <script id="listing_product_review_compact_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/_review-compact.mustache') ?>
    </script>

    <script id="listing_product_favorite_button_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/_favoriteButton.mustache') ?>
    </script>

    <script id="listing_product_variations_tmpl" type="text/html" data-partial="<?= $helper->json($partials) ?>">
        <?= file_get_contents(\App::config()->templateDir . '/product/variations.mustache') ?>
    </script>
<? };