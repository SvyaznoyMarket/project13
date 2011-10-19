<?php use_helper('I18N') ?>

<div class="pagehead">
  <div class="breadcrumbs">
    <?php if (has_slot('navigation')): ?>
      <?php include_slot('navigation') ?>
    <?php endif ?>
  </div>
  <div class="clear"></div>
  <?php if (has_slot('title')): ?>
    <h1><?php include_slot('title') ?></h1>
  <?php endif ?>
  <div class="searchbox">
    <?php include_component('search', 'form', array('searchString', $searchString)) ?>
  </div>
  <div class="searchtitle">Вы искали <span class="orange">&quot;<?php echo $searchString ?>&quot;</span>
    <?php include_partial('search/product_count', array('count' => $count)) ?>
  </div>

  <div class="line"></div>
</div>