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
    <script type="text/javascript" src="/js/jcookies.js"></script>
</head>

<body>
<script>
	$(document).ready(function(){
		var jc =  $.jCookies({ get : 'face' })
		if( !jc )
			jc = 1
console.info(jc)
		$.jCookies({
			name : 'face',
			value : ( jc + 1 ) % 6
		})	

		var node    = $('.bannersboxinner')
		var bignode = $('.bigbanner')
		bignode.find('img').attr('src','/images/banners/big/banner'+ jc +'.jpg')
		node.find('.banner2 img').attr('src','/images/banners/small/banner'+ (jc++) +'.png')
		node.find('.banner3 img').attr('src','/images/banners/medium/banner'+ (jc++) +'.png')		
		node.find('.banner4 img').attr('src','/images/banners/medium/banner'+ (jc++) +'.png')		
		node.find('.banner5 img').attr('src','/images/banners/medium/banner'+ (jc++) +'.png')				
		jc++
		for(var i=2; i <= 5; i++){
			var j = (jc++) % 5 
			if( !j ) j=5
			console.info(j)		
			node.find('.banner'+ i +' img').attr('src','/images/banners/small/banner'+ j +'.png')
		}
		
	})
</script>
<div class="bannersbox">
    <div class="bannersboxinner">
	  <!-- /images/banners/small/banner3.png -->
        <div class="banner banner2"><a href=""><img src="" alt="" width="148" height="132" /></a></div>
        <div class="banner zone banner3"><a href=""><img src="" alt="" width="159" height="186" /></a></div>
        <div class="banner zone banner4"><a href=""><img src="" alt="" width="182" height="236" /></a></div>
        <div class="banner banner5"><a href=""><img src="" alt="" width="142" height="128" /></a></div>
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
        <input type="text" class="text" value="Поиск среди 30 000 товаров" /><input type="button" class="searchbutton" value="Найти" title="Найти" />
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
