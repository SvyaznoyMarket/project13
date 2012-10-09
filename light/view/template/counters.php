<?php
namespace light;
if(!class_exists('light\Counters')){
  exit();
}

Counters::$counters['Yandex.Metrika'] = <<<YANDEX_METRIKA

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
YANDEX_METRIKA;

Counters::$counters['Admitad'] = <<<ADMITAD
<script type='text/javascript'><!--//<![CDATA[
var p = (location.protocol=='https:'?'https://delivery.ctasnet.com/adserver/www/delivery/tjs.php':'http://delivery.ctasnet.com/adserver/www/delivery/tjs.php');

var r=Math.floor(Math.random()*999999);
var r2=Math.floor(Math.random()*999999);
document.write ("<" + "script language='JavaScript' ");
document.write ("type='text/javascript' src='"+p);
document.write ("?trackerid=1287&amp;append=1&amp;r="+r);
if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
document.write("'><" + "\/script>");
//]]>--></script><noscript><div id='m3_tracker_1287' style='position: absolute; left: 0px; top: 0px; visibility: hidden;'><img src='http://delivery.ctasnet.com/adserver/www/delivery/ti.php?trackerid=1287&amp;cb='+r2 width='0' height='0' alt='' /></div></noscript>
ADMITAD;

Counters::$counters['Heias'] = <<<HEIAS
<script type="text/javascript">
  (function(d){
    var HEIAS_PARAMS = [];
    HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
    HEIAS_PARAMS.push(['pb', '1']);
    if (typeof window.HEIAS === 'undefined') { window.HEIAS = []; }
    window.HEIAS.push(HEIAS_PARAMS);
    var scr = d.createElement('script');
    scr.async = true;
    scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
    var elem = d.getElementsByTagName('script')[0];
    elem.parentNode.insertBefore(scr, elem);
  }(document));
</script>
HEIAS;

Counters::$counters['Adriver'] = sprintf(<<<ADRIVER
<!--  AdRiver code START. Type:counter(zeropixel) Site: Enter PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=182615&bt=21&pz=0&custom=10=%s;11=%s&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=182615&bt=21&pz=0&rnd=1157947727&custom=10=%s;11=%s" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->
ADRIVER
  ,Counters::getParam('productId'), Counters::getParam('categoryId'), Counters::getParam('productId'), Counters::getParam('categoryId')
);

Counters::$counters['AdriverOrder'] = sprintf(<<<ADRIVER_ORDER
<div id="adriverOrder" data-vars='%s' class="jsanalytics"></div>
ADRIVER_ORDER
  ,Counters::getParam('jsonOrderData')
);

Counters::$counters['HeiasOrder'] = sprintf(<<<HEIAS_ORDER
<div id="heiasComplete" data-vars='%s' class="jsanalytics"></div>
HEIAS_ORDER
  ,Counters::getParam('jsonOrderData')
);

Counters::$counters['AdHands'] = <<<ADHANDS
<!-- AdHands -->
    <script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
    <script type="text/javascript">
      var report = new adhandsReport ('http://sedu.adhands.ru/site/');
      report.id('1053');
      report.send();
    </script>
    <noscript>
      <img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;">
    </noscript>
<!-- /AdHands -->
ADHANDS;

Counters::$counters['Adblender'] = <<<ADBLENDER
<script type="text/javascript">
  (function() {
  document.write('<script type="text/javascript" src="' + ('https:' == document.location.protocol ? 'https://' : 'http://') + 'bn.adblender.ru/view.js?r=' + Math.random() + '" ></sc' + 'ript>');
  })();
</script>
ADBLENDER;

Counters::$counters['googleAnalytics'] = <<<GOOGLE_ANALYTICS
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
GOOGLE_ANALYTICS;

Counters::$counters['gooReMaQuickOrder'] = <<<gooReMaQuickOrder
<div id="gooReMaQuickOrder" class="jsanalytics"></div>
gooReMaQuickOrder;

Counters::$counters['EverestTech'] = sprintf(<<<EVERESTTECH
<!-- Efficient Frontiers -->
  <img src='http://pixel.everesttech.net/3252/t?ev_Orders=0&amp;ev_Revenue=0&amp;ev_Quickorders=1&amp;ev_Quickrevenue=%s&amp;ev_transid=%s' width='1' height='1'/>
EVERESTTECH
  ,Counters::getParam('orderData')->getTotalPrice(), Counters::getParam('orderData')->getNumber()
);


Counters::$blocks['mainPageHeader'] = array(
  'googleAnalytics'
);

Counters::$blocks['mainPage'] = array(
  'Yandex.Metrika',
  'AdHands',
  'Adblender',
  'Admitad',
  'Heias',
  'Adriver'
);

Counters::$blocks['order1ClickSuccess'] = array(
  'gooReMaQuickOrder',
  'AdriverOrder',
  'HeiasOrder',
  'EverestTech'
);