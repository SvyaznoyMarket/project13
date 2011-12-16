<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_combined_stylesheets() //include_stylesheets() ?>
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

<div class="bannersbox">
    <div class="bannersboxinner">
	  <!-- /images/banners/small/banner3.png -->
        <div class="banner banner2"><a href=""><img src="" alt="" /></a></div>
        <div class="banner banner3"><a href=""><img src="" alt="" /></a></div>
        <div class="banner banner4"><a href=""><img src="" alt="" /></a></div>
        <div class="banner banner5"><a href=""><img src="" alt="" /></a></div>
      <!-- -->
      <?php // include_component('default', 'slot', array('token' => 'banner_default')) ?>
    </div>
</div>


<div class="allpage">

    <div class="logo">Enter Связной</div>
      <!-- Topmenu -->
      <?php include_component('productCategory', 'root_list') ?>
      <!-- /Topmenu -->

    <noindex>
        <div class="searchbox">
          <?php include_component('search', 'form', array('view' => 'main')) ?>
        </div>
    </noindex>


    <div class="bigbanner"><a href=""><img src="" alt="" width="768" height="302" /></a></div>
    <?php // include_component('default', 'slot', array('token' => 'big_banner')) ?>


    <?php include_component('default', 'footer', array('view' => 'main')) ?>


    <div class="clear"></div>
</div>

<?php include_combined_javascripts() ?>
<script>
	$(document).ready(function(){

		function getRandomInt(min, max)
		{
		  return Math.floor(Math.random() * (max - min + 1)) + min
		}
		var pref = '/'
		var hrefs = [  'products/2060101001854', 'products/2060701001476', 'products/2060603000409', 'products/2050200005747', 
					'products/2040101007049',
					'products/2020103002174', 'products/2020301000941', 'products/2080502001192', 'products/2050100004444',
					'products/2050405000578', 
					'search?q=%D0%BA%D1%83%D1%85%D0%BD%D0%B8+%D1%81%D0%B5%D0%BB%D0%B5%D0%BD%D0%B0']
					//'2050301012576']
		var node    = $('.bannersboxinner')
		var bignode = $('.bigbanner')
		var bri = getRandomInt(1, 11)

		bignode.find('img').attr('src','/images/enter/big/enter'+ bri +'.jpg').parent().attr('href', pref + hrefs[bri - 1])
		var ri = getRandomInt(1, 4)
		while( bri == ri + 4 ) {
			ri = getRandomInt(1, 4)
		}

		node.find('.banner2 img').attr('src','/images/enter/small/enter'+ (ri + 4) +'.jpg')
								 .parent().attr('href', pref + hrefs[ri + 3])
		var ri_2 = getRandomInt(1, 4)
		while( ri_2 == ri || bri == ri_2 + 4 ) {
			ri_2 = getRandomInt(1, 4)
		}

		node.find('.banner5 img').attr('src','/images/enter/small/enter'+ (ri_2 + 4) +'.jpg')
								 .parent().attr('href', pref + hrefs[ri_2 + 3])

		ri = getRandomInt(1, 4)
		while( bri == ri ) {
			ri = getRandomInt(1, 4)
		}

		node.find('.banner3 img').attr('src','/images/enter/medium/enter'+ ri +'.jpg')
								 .parent().attr('href', pref + hrefs[ri - 1])
		ri_2 = ri
		while( ri_2 == ri || bri == ri_2 ) {
			ri_2 = getRandomInt(1, 4)
		}

		node.find('.banner4 img').attr('src','/images/enter/medium/enter'+ ri_2 +'.jpg')
								 .parent().attr('href', pref + hrefs[ri_2 - 1])

		$('.startse').bind ({ 'blur': function(){
				if (this.value == '')
					this.value = 'Поиск среди 20 000 товаров'
				},'focus': function() {
					if (this.value == 'Поиск среди 20 000 товаров')
						this.value = ''
					}
				})
	})
</script>
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
<img width="1" height="1" src="http://sedu.adhands.ru/site/?static=on&clid=1053&rnd=1234567890123" style="display:none;">
</noscript>
<!-- /AdHands -->

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>

</body>
</html>
