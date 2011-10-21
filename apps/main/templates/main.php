<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <base href="<?php echo $sf_request->getHost() ?>" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php include_javascripts() ?>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-25485956-2']);
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
    _gaq.push(['_trackPageview']);
    _gaq.push(['_trackPageLoadTime']);
    (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
</head>

<body>
<script>
	$(document).ready(function(){
		function getRandomInt(min, max)
		{
		  return Math.floor(Math.random() * (max - min + 1)) + min
		}
		var pref = '/products/'
		var hrefs = [  '2030000012503', '2060400005041', '2060101000062', '2010101001637', '2020201000751',
					'2050600002612', '2020401001060', '2080501001025', '2060201000627']
		var node    = $('.bannersboxinner')
		var bignode = $('.bigbanner')
		var bri = getRandomInt(1, 9)

		bignode.find('img').attr('src','/images/banners/big/banner'+ bri +'.jpg').parent().attr('href', pref + hrefs[bri - 1])
		var ri = getRandomInt(1, 4)
		while( bri == ri + 4 ) {
			ri = getRandomInt(1, 4)
		}

		node.find('.banner2 img').attr('src','/images/banners/small/banner'+ (ri + 4) +'.jpg')
								 .parent().attr('href', pref + hrefs[ri + 3])
		var ri_2 = getRandomInt(1, 4)
		while( ri_2 == ri || bri == ri_2 + 4 ) {
			ri_2 = getRandomInt(1, 4)
		}

		node.find('.banner5 img').attr('src','/images/banners/small/banner'+ (ri_2 + 4) +'.jpg')
								 .parent().attr('href', pref + hrefs[ri_2 + 3])

		ri = getRandomInt(1, 4)
		while( bri == ri ) {
			ri = getRandomInt(1, 4)
		}

		node.find('.banner3 img').attr('src','/images/banners/medium/banner'+ ri +'.jpg')
								 .parent().attr('href', pref + hrefs[ri - 1])
		ri_2 = ri
		while( ri_2 == ri || bri == ri_2 ) {
			ri_2 = getRandomInt(1, 4)
		}

		node.find('.banner4 img').attr('src','/images/banners/medium/banner'+ ri_2 +'.jpg')
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
<div class="bannersbox">
    <div class="bannersboxinner">
	  <!-- /images/banners/small/banner3.png -->
        <div class="banner banner2"><a href=""><img src="" alt="" width="auto" height="auto" /></a></div>
        <div class="banner banner3"><a href=""><img src="" alt="" width="auto" height="auto" /></a></div>
        <div class="banner banner4"><a href=""><img src="" alt="" width="auto" height="auto" /></a></div>
        <div class="banner banner5"><a href=""><img src="" alt="" width="auto" height="auto" /></a></div>
      <!-- -->
      <?php // include_component('default', 'slot', array('token' => 'banner_default')) ?>
    </div>
</div>


<div class="allpage">

    <div class="logo">Enter Связной</div>
      <!-- Topmenu -->
      <?php include_component('productCategory', 'root_list') ?>
      <!-- /Topmenu -->

    <div class="searchbox">
      <?php include_component('search', 'form', array('view' => 'main')) ?>
    </div>


    <div class="bigbanner"><a href=""><img src="" alt="" width="768" height="302" /></a></div>
      <?php // include_component('default', 'slot', array('token' => 'big_banner')) ?>


    <?php include_component('default', 'footer', array('view' => 'main')) ?>


    <div class="clear"></div>
</div>

<!-- Yandex.Metrika counter -->
<div style="display:none;"><script type="text/javascript">
(function(w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter10067653 = new Ya.Metrika({id:10067653, enableAll: true});
        }
        catch(e) { }
    });
})(window, "yandex_metrika_callbacks");
</script></div>
<script src="//mc.yandex.ru/metrika/watch.js" type="text/javascript" defer="defer"></script>
<noscript><div><img src="//mc.yandex.ru/watch/10067653" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
</body>
</html>
