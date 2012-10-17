<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
    <?php include_title() ?>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <?php //include_javascripts() ?>
    <?php include_component('page', 'link_rel_canonical') ?>

    <?php include_partial('default/googleAnalytics') ?>
    
  </head>
  <body data-template="cart">
    <div class="allpage" id="page">
	 <div class="adfoxWrapper" id="adfoxbground"></div>
     <div class="allpageinner buyingpage">

        <?php include_partial('default/header') ?>

        <!-- Page head -->
        <?php if (!include_slot('page_head')): ?>
          <?php include_partial('default/page_head') ?>
        <?php endif ?>
        <!-- Page head -->

        <?php if (has_slot('left_column')): ?>
          <div class="float100">
            <div class="column685">
              <?php echo $sf_content ?>
            </div>
          </div>
          <div class="column215">
            <?php include_slot('left_column') ?>
          </div>
        <?php else: ?>
          <?php echo $sf_content ?>
        <?php endif ?>
        <div class="clear"></div>
      <?php if (has_slot('navigation_seo')) include_slot('navigation_seo') ?>
      </div>
      <div class="clear"></div>
    </div>
    <ul class="bBuyingFooter">
        <li>
            <div class="bBuyingFooter__eEnter"></div>
            <h3>Ответы на вопросы</h3>
            <span>Наш Контакт-сENTER<br><b>8 (800) 700 00 09</b><br> 24 часа в сутки / 7 дней в неделю. Звонок бесплатный. Радость в подарок :)</span>
        </li>
        <li>

            <div class="bBuyingFooter__eZakaz"></div>
            <h3>Безопасные покупки</h3>
            <span>Вы приобретаете качественный товар. Получаете и оплачиваете любым удобным для Вас способом.</span>
        </li>
        <li>
            <div class="bBuyingFooter__ePeople"></div>
            <h3>Сопровождение заказа</h3>

            <span>После оформления заказа, с Вами свяжется специалист нашего Контакт-сENTER для подтверждения заказа.</span>
        </li>
        <li>
            <div class="bBuyingFooter__eCar"></div>
            <h3>Собственная служба доставки и сервис</h3>
            <span>Наша служба F1 доставит заказ вовремя.<br>Соберет, настроит и покажет, как работает.</span>
        </li>

        <li>
            <div class="bBuyingFooter__eFinger"></div>
            <h3>Как для себя</h3>
            <span>Вы можете обменять товар в течение 30 дней<br> и в течение 14 дней вернуть в магазин. </span>
        </li>
    </ul>

    <?php include_component('default', 'footer', array('view' => 'compact', )) ?>

    <?php if (!include_slot('auth')) include_partial('default/auth') ?>

    <?php include_component('region', 'select') ?>

    <?php //include_partial('default/admin') ?>
<script src="/js/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>
<script type="text/javascript">
  var mtHost = (("https:" == document.location.protocol) ? "https://rainbowx" : "http://rainbowx") + ".mythings.com";
  var mtAdvertiserToken = "1989-100-ru";
  document.write(unescape("%3Cscript src='" + mtHost + "/c.aspx?atok=" + mtAdvertiserToken + "' type='text/javascript'%3E%3C/script%3E")); 
</script>
<?php if ('live' == sfConfig::get('sf_environment')): ?>
  <?php include_partial('default/yandexMetrika') ?>
   <div id="adblender" class="jsanalytics"></div>
<?php endif ?>
<div id="gooReMaCart" class="jsanalytics"></div>
<div id="luxupTracker" class="jsanalytics"></div>

<?php if (has_slot('seo_counters_advance')): ?>
  <?php include_slot('seo_counters_advance') ?>
<?php endif ?>

<?php include_component('default', 'adriver') ?>
  </body>
</html>
