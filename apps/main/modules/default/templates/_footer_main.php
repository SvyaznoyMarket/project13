<div id="mainPageFooter">
  <div class='bIndexLinks2__eBan'>
    <div class="vcardtitle">Контакт-сENTER</div>
    <div class="vcard"><span class="tel">8 (800) 700 00 09</span></div>
    <div class="address">
      Звонок бесплатный. Радость в подарок :)
      <?php if (sfConfig::get('app_online_call_enabled')): ?>
      <br/>
      <a class="bCall"
         onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false"
         href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить онлайн</a>
      <?php endif ?>
    </div>
  </div>
  <div class='bIndexLinks2__eBan'><a href="/pdf/barometr_enter.pdf"><img
    src='/images/barometr-ava.jpg'></a></div>
  <div class='bIndexLinks2__eBan'><a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>"><img
    src='/images/img_main_f1service.jpg'></a></div>
  <div class='bIndexLinks2__eLinks'>
    <a href="<?php echo url_for('wordpress', array('page' => 'about_company',)) ?>">О компании</a>
    <a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>">Сервис F1</a>
    <a href="<?php echo url_for('wordpress', array('page' => 'credit',)) ?>">Покупка в кредит</a>
    <a href="http://job.enter.ru/vacancies" target="_blank">Работать у нас</a>
    <a href="<?php echo url_for('shop') ?>">Наши магазины</a>
    <a href="<?php echo url_for('wordpress', array('page' => 'how_make_order',)) ?>">Как сделать заказ</a>
    <a href="<?php echo url_for('wordpress', array('page' => 'mobile_apps')) ?>">Мобильные приложения</a>
    <a href="http://feedback.enter.ru/">Обратная связь</a>
  </div>

  <div class="copy">
    <div class="social_network"><span class="gray font11">Посетите нас</span>
      <a href="http://twitter.com/#!/enter_ru" title="twitter" target="_blank"></a>
      <a href="http://www.facebook.com/enter.ru" title="facebook" target="_blank"></a>
      <a href="http://vkontakte.ru/public31456119" title="vkontakte" target="_blank"></a>
    </div>
    <div class='pb20'>
      Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.<br>
      <a href="<?php echo url_for('wordpress', array('page' => 'legal')) ?>">Правовая информация</a>
      <a href="<?php echo url_for('wordpress', array('page' => 'terms')) ?>">Условия продажи</a>
      <a href="<?php echo url_for('wordpress', array('page' => 'media_info')) ?>">Информация о СМИ</a>
      <a href="<?php echo url_for('user') ?>">Личный кабинет</a>
    </div><br/>
    <span class="rights"> &copy; ООО &laquo;Энтер&raquo; 2011&ndash;<?php echo date("Y") ?>. <span style="font-size: 11px;"> ENTER<sup>&reg;</sup> ЕНТЕР<sup>&reg;</sup> Enter<sup>&reg;</sup>.</span> Все права защищены.</span>
  </div>
</div>
