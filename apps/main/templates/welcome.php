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

    <?php include_partial('default/googleAnalytics') ?>

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



<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>

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
<?php endif ?>

  </body>
</html>