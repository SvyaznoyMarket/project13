<?php /* @var $product ProductEntity */ ?>

<?php slot('header_meta_og') ?>

<?php end_slot() ?>

<?php slot('navigation') ?>

<?php end_slot() ?>

<?php slot('title', $product->getName()) ?>

<?php include_partial('product_/show_card', $sf_data) ?>

<br class="clear"/>

<?php include_partial('productCard_/seo', $sf_data) ?>
