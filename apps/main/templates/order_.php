<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<body data-template="<?php echo $sf_request->getParameter('_template') ?>">
<div class="graying" style="opacity: 0.5; display: none;"></div>
<?php include_slot('js_template') ?>

<div class="allpage" data="privet">
  <div class="allpageinner buyingpage">

    <?php echo $sf_content ?>

  </div>
</div>

<?php include_component('default', 'footer', array('view' => 'compact')) ?>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>

<?php if (!include_slot('auth')) include_partial('default/auth') ?>

<?php if ('live' == sfConfig::get('sf_environment')): ?>
<!-- Yandex.Metrika counter -->
<div style="display:none;">
  <script type="text/javascript">
    (function (w, c) {
      (w[c] = w[c] || []).push(function () {
        try {
          w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll:true, webvisor:true, params:window.yaParams || { }});
        }
        catch (e) {
        }
      });
    })(window, "yandex_metrika_callbacks");
  </script>
</div>
<script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
<noscript>
  <div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt=""/></div>
</noscript>
<!-- /Yandex.Metrika counter -->

<!-- AdHands -->
<?php if (has_slot('adhands_report')) include_slot('adhands_report') ?>

<?php endif ?>

<?php if (has_slot('seo_counters_advance')) include_slot('seo_counters_advance') ?>

</body>
</html>
