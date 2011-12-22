<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <link rel="stylesheet" type="text/css" href="/css/jquery.countdown.css"/>
    <link rel="stylesheet" type="text/css" href="/css/font.css"/>
    <?php include_javascripts() ?>
    <script type="text/javascript" src="/js/jquery-1.6.4.min.js"></script>
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
<!--script>
$(document).ready(function(){
	function relme () {
		location.reload(true)
	}
	var austDay = new Date()
	austDay = new Date(austDay.getFullYear() , 11 - 1, 16)
	$('#cd').countdown({ until: austDay , format: 'HMS', onExpiry: relme, expiryText: 'Перезагрузка страницы' })
})
</script-->
  </head>

  <body>
    <?php LastModifiedHandler::setLastModified();  ?>
    <div class="allpage">

      <div class="entry">
        <div class="entrybox">
          <?php echo $sf_content ?>
        </div>

        <!--
        <div class="openblock">

          <h1>
          Скоро открытие!
          </h1>
          <div id="cd"></div>
        </div>
        -->

      </div>

    </div>

<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter10503055 = new Ya.Metrika({id:10503055, enableAll: true, webvisor:true});
        }
        catch(e) { }
    });
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch_visor.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/10503055" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- AdHands -->
<script type="text/javascript" src="http://sedu.adhands.ru/js/counter.js"></script>
<script type="text/javascript">
    var report = new adhandsReport ('http://sedu.adhands.ru/site/');
    report.id('1053');
    report.send();
</script>
<noscript>
<img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;" />
</noscript>
<!-- /AdHands -->
  </body>
</html>