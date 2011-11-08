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

  <noindex>  
      <div class="searchbox">
        <?php include_component('search', 'form', array('searchString', $searchString)) ?>
      </div>
  </noindex>

  <?php include_partial('search/product_count', $sf_data) ?>

  <div class="line"></div>

</div>