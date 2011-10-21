<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php include_javascripts() ?>
</head>

<body>
<div class="allpage">
<div class="allpageinner buyingpage">

    <!-- Header -->
    <div class="basketheader">
      <?php include_partial('default/logo') ?>
      <?php include_slot('step') ?>
        <div class="headerright" style="font-size: 14px; font-family: Enter;">
            Заказ и консультации
            <div class="vcard"><div class="tel"><span>(800)</span>700-00-09</div></div>
        </div>
    </div>
    <!-- /Header -->


    <!-- Page head -->
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
        <div class="line"></div>
    </div>
    <!-- Page head -->

    <div class="clear"></div>

    <?php if (has_slot('receipt')): ?>
      <?php include_slot('receipt') ?>
    <?php endif ?>

    <?php echo $sf_content ?>

    <div class="clear"></div>

</div>
</div>

<?php include_component('default', 'footer', array('view' => 'compact')) ?>

</body>
</html>
