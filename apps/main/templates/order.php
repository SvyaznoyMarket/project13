<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php //include_javascripts() ?>
  <?php include_component('page', 'link_rel_canonical') ?>

  <?php include_partial('default/googleAnalytics') ?>
</head>

<body data-template="order">
<?php LastModifiedHandler::setLastModified(); ?>
<div class="allpage" id="page">
	<div class="adfoxWrapper" id="adfoxbground"></div>
  <div class="allpageinner buyingpage">

    <!-- Header -->
    <div class="basketheader">
      <div class="bNLogo"><?php include_slot('title') ?></div>
      <div class="headerright mNLogo">
        <h2>Круглосуточный Контакт-сEnter</h2>
        <div>8 (800) 700-00-09</div>

        <span>Звонок бесплатный. Радость в подарок :)</span>
      </div>
    </div>
    <!-- /Header -->

    <!-- Page head -->
    <div class="pagehead">
      <div class="clear"></div>
      <div class="line"></div>
      <?php if (has_slot('user')): ?>
      <div class='bFormAuth'>
        <?php include_slot('user') ?>
      </div>
      <div class="line"></div>
      <?php endif ?>
    </div>
    <!-- Page head -->

    <div class="clear"></div>

    <?php if (has_slot('receipt')): ?>
    <?php include_slot('receipt') ?>
    <?php endif ?>

    <?php echo $sf_content ?>

    <div class="clear"></div>
    <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>

  </div>
</div>

<?php include_component('default', 'footer', array('view' => 'compact')) ?>
<script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>
<script type="text/javascript">
  var mtHost = (("https:" == document.location.protocol) ? "https://rainbowx" : "http://rainbowx") + ".mythings.com";
  var mtAdvertiserToken = "1989-100-ru";
  document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok=" + mtAdvertiserToken + "' type='text/javascript'%3E%3C/script%3E")); 
</script>
<?php if (!include_slot('auth')) include_partial('default/auth') ?>

<?php include_partial('region/select') ?>


<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>
  <div id="adblender" class="jsanalytics"></div>
  <?php endif ?>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
  <?php endif ?>
<?php include_component('default', 'adriver') ?>
<div id="luxupTracker" class="jsanalytics"></div>

</body>
</html>
