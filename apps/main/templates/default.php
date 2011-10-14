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
</head>

<body>
<script>
	$(document).ready(function(){
		function getRandomInt(min, max)
		{
		  return Math.floor(Math.random() * (max - min + 1)) + min
		}
		var pref = '/products/'
		var hrefs = [  '2030000012503', '2010106001809', '2060101000062', '2010101001637', '2020201000751', 
					'2050600002612', '2020401001060', '2070201000046', '2060201000627']
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
    <form action="">
        <input type="text" class="text startse" value="Поиск среди 20 000 товаров" /><input type="button" class="searchbutton" value="Найти" title="Найти" />
    </form>
    </div>


    <div class="bigbanner"><a href=""><img src="" alt="" width="768" height="302" /></a></div>
      <?php // include_component('default', 'slot', array('token' => 'big_banner')) ?>


    <div class="content">
        <div class="vcardtitle">Контакт с ENTER</div>
        <div class="vcard"><span class="tel">8 (800) 700 00 09</span></div>
        <div class="address">Звонок бесплатный. Радость в подарок :)</div>
    </div>


    <div class="links">
        <a href="#" class="link1">Сервис</a>
        <a href="http://www.svyaznoybank.ru/" class="link2">Связной</a>
        <a href="http://www.svyaznoybank.ru/" class="link3">Финансовые услуги</a>
    </div>

    <div class="copy">
        <div class="pb5">&copy; &laquo;Enter&raquo; 2011. Все права защищены. Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату. <a href="/">Условия продажи</a></div>
    </div>


    <div class="clear"></div>
</div>

</body>
</html>
