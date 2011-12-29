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
		var values = {
			'2060302001844': '/products/2060302001844',
			'2050600000380': '/products/2050600000380',
//			'selena': '/search?q=%D1%81%D0%B5%D0%BB%D0%B5%D0%BD%D0%B0&product_type=677',
			'selena': '/products/set/2050301012569,2050301012576,2050301012538,2050301012521,2050301012552,2050301012545',
//			'2010103001529': '/products/2010103001529',
			'2080502000751': '/products/2080502000751',
			'2030000018871': '/products/2030000018871',
			'2010203001047': '/products/2010203001047',
			'2020301001603': '/products/2020301001603',
			'2060101001236': '/products/2060101001236',
			'2040302000771': '/products/2040302000771',//гирлянда			
			'2070404000232': '/products/2070404000232',//коньки
			'2030000048403': '/products/2030000048403',//часы
			'2040302000412': '/products/2040302000412',//елка
			'samsonite': 'product/sport/samsonite-chemodan-samsonite-atolas-65-cherniy', //чемодан
			'2040404000051': '/products/2040404000051',//светильник
			'2020103001443': '/products/2020103001443',//блендер
			'2040101007056': '/products/2040101007056',//посуда
			'2040302001983': '/products/2040302001983'//снеговик
			
		}
		var rotations = {
		// images/enter/big/ file by file
			big:['2060302001844','2050600000380','selena','2080502000751','2030000018871','2010203001047',
				 '2020301001603','2060101001236','2040302000771','2070404000232','2030000048403','2040302000412','samsonite',
				 '2040404000051','2020103001443','2040101007056','2040302001983'],
		// images/enter/medium/ file by file
			med: ['2060101001236','2080502000751','2010203001047','2030000048403','2070404000232','2040302000412',
				'2040302001983','2040404000051'],
		// images/enter/small/ file by file
			sm: ['2020301001603','2030000018871','2060302001844','2050600000380','2040302000771','samsonite']
		}

		var node    = $('.bannersboxinner')
		var bignode = $('.bigbanner')
		var ind = getRandomInt(0, rotations.big.length - 1 )
		var bri = rotations.big[ ind ] + ''
		bignode.find('img').attr('src','/images/enter/big/enter_'+ (ind*1+1) +'.jpg')
						   .parent().attr('href', values[bri])

		ind = getRandomInt(0, rotations.med.length - 1 )
		var mri = rotations.med[ ind ] + ''
		while( bri === mri ) {
			ind = getRandomInt(0, rotations.med.length - 1 )
			mri = rotations.med[ ind ] + ''
		}
		var mri_2 = mri
		node.find('.banner3 img').attr('src','/images/enter/medium/enter_'+ (ind*1+1) +'.jpg')
								 .parent().attr('href', values[mri])
		ind = getRandomInt(0, rotations.med.length - 1 )
		var mri = rotations.med[ ind ] + ''

		while( bri === mri || mri_2 === mri) {
			ind = getRandomInt(0, rotations.med.length - 1 )
			mri = rotations.med[ ind ] + ''
		}
		node.find('.banner4 img').attr('src','/images/enter/medium/enter_'+ (ind*1+1) +'.jpg')
								 .parent().attr('href', values[mri])

		ind = getRandomInt(0, rotations.sm.length - 1 )
		var sri = rotations.sm[ ind ] + ''
		while( bri === sri || mri === sri || mri_2 === sri) {
			ind = getRandomInt(0, rotations.sm.length - 1 )
			sri = rotations.sm[ ind ] + ''
		}
		var sri_2 = sri
		node.find('.banner2 img').attr('src','/images/enter/small/enter_'+ (ind*1+1) +'.jpg')
								 .parent().attr('href', values[sri])
		ind = getRandomInt(0, rotations.sm.length - 1 )
		var sri = rotations.sm[ ind ] + ''
		while( bri === sri || mri === sri || mri_2 === sri || sri_2 === sri) {
			ind = getRandomInt(0, rotations.sm.length - 1 )
			sri = rotations.sm[ ind ] + ''
		}
		node.find('.banner5 img').attr('src','/images/enter/small/enter_'+ (ind*1+1) +'.jpg')
								 .parent().attr('href', values[sri])

		$('.startse').bind ({
			'blur': function() {
				if (this.value == '')
					this.value = 'Поиск среди 20 000 товаров'
			},
			'focus': function() {
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
