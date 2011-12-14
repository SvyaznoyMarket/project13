<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <?php include_http_metas() ?>
  <?php include_metas() ?>
  <?php include_title() ?>
  <?php include_stylesheets() ?>
  <?php //include_javascripts() ?>
  <?php include_component('page', 'link_rel_canonical') ?>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-25485956-1']);
_gaq.push(['_addOrganic', 'nova.rambler.ru', 'query']);
_gaq.push(['_addOrganic', 'go.mail.ru', 'q']);
_gaq.push(['_addOrganic', 'nigma.ru', 's']);
_gaq.push(['_addOrganic', 'webalta.ru', 'q']);
_gaq.push(['_addOrganic', 'aport.ru', 'r']);
_gaq.push(['_addOrganic', 'poisk.ru', 'text']);
_gaq.push(['_addOrganic', 'km.ru', 'sq']);
_gaq.push(['_addOrganic', 'liveinternet.ru', 'ask']);
_gaq.push(['_addOrganic', 'quintura.ru', 'request']);
_gaq.push(['_addOrganic', 'search.qip.ru', 'query']);
_gaq.push(['_addOrganic', 'gde.ru', 'keywords']);
_gaq.push(['_addOrganic', 'gogo.ru', 'q']);
_gaq.push(['_addOrganic', 'ru.yahoo.com', 'p']);
_gaq.push(['_addOrganic', 'images.yandex.ru', 'q', true]);
_gaq.push(['_addOrganic', 'blogsearch.google.ru', 'q', true]);
_gaq.push(['_addOrganic', 'blogs.yandex.ru', 'text', true]);
_gaq.push(['_addOrganic', 'ru.search.yahoo.com','p']);
_gaq.push(['_addOrganic', 'ya.ru', 'q']);
_gaq.push(['_addOrganic', 'm.yandex.ru','query']);
_gaq.push(['_trackPageview']);
_gaq.push(['_trackPageLoadTime']);
(function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })();
</script>
</head>

<body>
<?php LastModifiedHandler::setLastModified();  ?>
<div class="allpage">
<div class="allpageinner buyingpage">

    <!-- Header -->
    <div class="basketheader">
      <?php include_partial('default/logo') ?>
      <?php include_slot('step') ?>
        <div class="headerright" style="font-size: 14px; font-family: Enter; padding-top: 31px;">
            Круглосуточный контакт cENTER
            <div class="vcard"><div class="tel font24" style="font-size: 24px; line-height: 24px;">8 (800) 700-00-09<br /><span style="font-size: 10px;" class="gray">Звонок бесплатный. Радость в подарок :)</span></div></div>
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
    <?php include_javascripts() ?>
<?php include_component('default', 'footer', array('view' => 'compact')) ?>

<?php if (!include_slot('auth')) include_partial('default/auth') ?>

<?php include_combined_javascripts() ?>

<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true});
        }
        catch(e) { }
    });
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript"></script>
<noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<script>
var yaParams
if( yaParams )
	window.yaCounter10503055.params(yaParams);
</script>
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
    var report = new adhandsReport ('http://sedu.adhands.ru/site/');
    report.id('1053');
    <?php
        if (isset($orderSum) && $orderSum>0 && isset($orderId) && $orderId>0){
            echo
    "report.data('am','".$orderSum."');
    report.data('ordid','".$orderId."');
"
                ;
        }
    ?>
    report.send();
</script>
<noscript>
<img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;">
</noscript>
<!-- /AdHands -->
</body>
</html>
