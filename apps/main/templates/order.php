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

<body>

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
  <?php
  //если получена сумма и id заказа, добавим доплонительный код в adHands
  ob_start();
  include_slot('complete_order_sum');
  $orderSum = ob_get_contents();
  ob_end_clean();
  ob_start();
  include_slot('complete_order_id');
  $orderId = ob_get_contents();
  ob_end_clean();
  ?>
<script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
<script type="text/javascript">
  var report = new adhandsReport('http://sedu.adhands.ru/site/');
  report.id('1053');
    <?php
    if (isset($orderSum) && $orderSum > 0 && isset($orderId) && $orderId > 0)
    {
      echo
        "report.data('am','" . $orderSum . "');
    report.data('ordid','" . $orderId . "');
";
    }
    ?>
  report.send();
</script>
<noscript>
  <img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123"
       style="display:none;">
</noscript>
<!-- /AdHands -->
<script type="text/javascript">
  (function () {
    <?php if (isset($orderSum) && $orderSum > 0 && isset($orderId) && $orderId > 0): ?>
      var orderSum = '<?php echo $orderSum ?>';
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/pixel.js?cost=' + escape(orderSum) + '&r=' + Math.random() + '" ></sc' + 'ript>');
      <?php else: ?>
      document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>');
      <?php endif ?>
  })();
</script>
<?php endif ?>

<?php if (has_slot('seo_counters_advance')) include_slot('seo_counters_advance') ?>

</body>
</html>

