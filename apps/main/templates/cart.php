<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php //include_javascripts() ?>
    <?php include_component('page', 'link_rel_canonical') ?>

    <?php include_partial('default/googleAnalytics') ?>
    
  </head>
  <body data-template="cart">
    <div class="allpage" id="page">
	 <div class="adfoxWrapper" id="adfoxbground"></div>
     <div class="allpageinner">

        <?php include_partial('default/header') ?>

        <!-- Page head -->
        <?php if (!include_slot('page_head')): ?>
          <?php include_partial('default/page_head') ?>
        <?php endif ?>
        <!-- Page head -->

        <?php if (has_slot('left_column')): ?>
          <div class="float100">
            <div class="column685">
              <?php echo $sf_content ?>
            </div>
          </div>
          <div class="column215">
            <?php include_slot('left_column') ?>
          </div>
        <?php else: ?>
          <?php echo $sf_content ?>
        <?php endif ?>
        <div class="clear"></div>
      <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>
      </div>
      <div class="clear"></div>
    </div>

    <?php include_component('default', 'footer', array('class' => 'footer_cart', )) ?>

    <?php if (!include_slot('auth')) include_partial('default/auth') ?>

    <?php include_component('region', 'select') ?>

    <?php //include_partial('default/admin') ?>
<script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>
<script type="text/javascript">
  var mtHost = (("https:" == document.location.protocol) ? "https://rainbowx" : "http://rainbowx") + ".mythings.com";
  var mtAdvertiserToken = "1989-100-ru";
  document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok=" + mtAdvertiserToken + "' type='text/javascript'%3E%3C/script%3E")); 
</script>
<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>
   <div id="adblender" class="jsanalytics"></div>
<?php endif ?>
<div id="gooReMaCart" class="jsanalytics"></div>
<div id="luxupTracker" class="jsanalytics"></div>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>

<?php include_component('default', 'adriver') ?>
  </body>
</html>
