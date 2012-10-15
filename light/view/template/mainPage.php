<?php
namespace light;

/**
 * @var $promoArray array
 * @var $rootCategoryList CategoryShortData[]
 * @var $this HtmlRenderer
 */

require_once(Config::get('helperPath').'Counters.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="yandex-verification" content="623bb356993d4993" />
    <meta name="viewport" content="width=900" />
    <meta name="title" content="<?php echo $this->getTitle(); ?>" />
    <title><?php echo $this->getTitle(); ?></title>
    <meta name="description" content="<?php echo $this->getDescription(); ?>" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php $this->showCss() ?>
    <link rel="canonical" href="http://www.enter.ru" />

    <?php echo Counters::getBlock('mainPageHeader'); ?>
  </head>

<body data-template="main">

<div class="bannersbox">
  <div class="bannersboxinner">
    <div class="banner banner3"><img class="rightImage" src="" alt="" /></div>
    <div class="banner banner4"><img class="leftImage" src="" alt="" /></div>
  </div>
</div>

<input id="main_banner-data" type="hidden" disabled="disabled" data-value='<?php echo str_replace("'", "&#39;", json_encode($promoArray)) ?>' />

<div class="allpage">

  <div class="bHeaderWrap">
    <div class="bHeader">
      <a href class='bToplogo'></a>
      <?php require('category/rootCategoryList.php'); ?>
      <div class="bHeader__eLong"></div>
    </div>
  </div>

  <noindex>
    <div class="searchbox">
      <form class="search-form" action="<?php echo $this->url('search.form') ?>" method="get">
        <input name="q" type="text" class="text startse" value="Поиск среди 20 000 товаров" />
        <input type="submit" class="searchbutton" value="Найти" title="Найти" />
      </form>
    </div>
  </noindex>

  <div class="bigbanner">
    <div class='bCarouselWrap'>
      <div class='bCarousel'>
        <div class='bCarousel__eBtnL leftArrow'></div>
        <div class='bCarousel__eBtnR rightArrow'></div>
        <img class="centerImage" src="" alt=""/>
      </div>
    </div>
  </div>

  <div class='bIndexLinks2'>
    <div class='bIndexLinks2__eBanners'>
      <div class='bIndexLinks2__eBan'>
        <div class="vcardtitle">Контакт с ENTER</div>
        <div class="vcard"><span class="tel">8 (800) 700 00 09</span></div>
        <div class="address">
          Звонок бесплатный. Радость в подарок :)
          <?php if (Config::get('onlineCallEnabled')): ?>
          <br/>
          <a class="bCall"
             onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false"
             href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
          <?php endif ?>
        </div>
      </div>
      <div class='bIndexLinks2__eBan'><a href="/pdf/research_enter.pdf" target="_blank"><img
        src='/images/img_main_research_enter.jpg'></a></div>
      <div class='bIndexLinks2__eBan'><a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'f1')) ?>"><img
        src='/images/img_main_f1service.jpg'></a></div>
      <div class='bIndexLinks2__eLinks'>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'about_company')) ?>">О компании</a>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'f1')) ?>">Сервис F1</a>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'credit')) ?>">Покупка в кредит</a>
        <a href="http://job.enter.ru/" target="_blank">Работать у нас</a>
        <a href="<?php echo $this->url('shop.regionList') ?>">Наши магазины</a>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'how_make_order')) ?>">Как сделать заказ</a>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'mobile_apps')) ?>">Мобильные приложения</a>
        <a href="<?php echo $this->url('staticPage.content', array('pageToken' => 'callback')) ?>">Обратная связь</a>
      </div>
    </div>

    <div class="copy">
      <div class="social_network"><span class="gray font11">Посетите нас</span>
        <a href="http://twitter.com/#!/enter_ru" title="twitter" target="_blank"></a>
        <a href="http://www.facebook.com/enter.ru" title="facebook" target="_blank"></a>
        <a href="http://vkontakte.ru/public31456119" title="vkontakte" target="_blank"></a>

        <div style="display:none"><img src="/css/skin/img/icon_vkontakte_color.png"/><img
          src="/css/skin/img/icon_twitter_color.png"/><img src="/css/skin/img/icon_facebook_color.png"/></div>
      </div>
      <div class="pb5">
        &copy; &laquo;Enter&raquo; 2011&ndash;<?php echo date("Y") ?>. Все права защищены. <a
        href='<?php echo $this->url('staticPage.content', array('pageToken' => 'terms')) ?>'>Условия продажи</a> <a
        href='<?php echo $this->url('staticPage.content', array('pageToken' => 'legal')) ?>'>Правовая информация</a> <a
        href="<?php echo $this->url('user.index') ?>">Личный кабинет</a></div>
    </div>
  </div>

  <div class="clear"></div>
</div>

<script src="/js/LAB.min.js" type="text/javascript"></script>
<script src="/js/loadjs.js" type="text/javascript"></script>


<?php echo Counters::getBlock('mainPage'); ?>

</body>
</html>
