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
    <?php include_component('search', 'form') ?>
  </div>
  <div class="clear pb20"></div>
  <!--<div class="line"></div>-->
</div>