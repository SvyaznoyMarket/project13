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

<?php include_component('region', 'select') ?>

<?php include_partial('order_/footer') ?>
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


<?php if (true || 'live' == sfConfig::get('sf_environment')) { ?>

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true});
            } catch(e) {}
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->

    <?php if (has_slot('analytics_report')) include_slot('analytics_report') ?>

<!-- Google Code for 'Тег ремаркетинга' -->
<!-- Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. For instructions on adding this tag and more information on the above requirements, read the setup guide: google.com/ads/remarketingsetup -->
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 1001659580;
    var google_conversion_label = "nphXCKzK6wMQvLnQ3QM";
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
        <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1001659580/?value=0&amp;label=nphXCKzK6wMQvLnQ3QM&amp;guid=ON&amp;script=0"/>
    </div>
</noscript>

<?php } //endif ?>

<?php if (has_slot('seo_counters_advance')) include_slot('seo_counters_advance') ?>
<div id="luxupTracker" class="jsanalytics"></div>

</body>
</html>
