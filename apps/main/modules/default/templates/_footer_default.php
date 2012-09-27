<!-- Footer -->
<div class="footer<?php if (isset($class)) echo " ".$class ?>">
  <div class="bFooter">
    <div class='bFooter__eLeft'>
      <div class='bFooter__eLogo'></div>
      <a href="<?php echo url_for('wordpress', array('page' => 'about_company',)) ?>">О Компании</a><br>
      <a href="<?php echo url_for('shop') ?>">Наши магазины</a><br>
      <a href="http://job.enter.ru/vacancies" target="_blank">Работать у нас</a><br>
      <a href="<?php echo url_for('callback') ?>">Напишите нам</a><br>
      <span>Еще больше<br> интересного<br> для вас в:</span><br>
      <a href="http://www.facebook.com/enter.ru" title="facebook" target="blank"></a>
      <a href="http://twitter.com/#!/enter_ru" title="twitter" target="blank"></a>
      <a href="http://vkontakte.ru/public31456119" title="vkontakte" target="blank"></a>
    </div>

    <dl class='bFooter__eRight'>
      <dt>Как это работает</dt>
      <dd>
        <div class='mFDiv mFoot1'>
          <h3>1. Заказываю товар</h3>

          <ul>
            <li>в Контакт-cEnter<br>
              <b class='mFphone'>8 (800) 700 00 09</b><br>
              <div class="bBottommenu__eSkype">
                <?php if (sfConfig::get('app_online_call_enabled')): ?>
                  <i class="mCall"><a class="bCall" onclick="typeof(_gaq)=='undefined'?'':_gaq.push(['_trackEvent', 'Zingaya', 'ButtonClick']);typeof(_gat)=='undefined'?'':_gat._getTrackerByName()._setAllowLinker(true); window.open(typeof(_gat)=='undefined'?this.href+'?referrer='+escape(window.location.href):_gat._getTrackerByName()._getLinkerUrl(this.href+'?referrer='+escape(window.location.href)), '_blank', 'width=236,height=220,resizable=no,toolbar=no,menubar=no,location=no,status=no'); return false" href="http://zingaya.com/widget/e990d486d664dfcff5f469b52f6bdb62">Позвонить с сайта</a></i>
                  <b><br></b>
                <?php endif ?>
                <i>Skype<b>:</b></i><b>skype2enter и call2enter<br></b>
                <i>ICQ<b>:</b></i><b>648198963</b></div>
            </li>
            <li>Через сайт или по каталогу</li>
            <li>Терминалы в магазине</li>
            <li><a href="<?php echo url_for('wordpress', array('page' => 'mobile_apps')) ?>">Мобильные и социальные приложения</a></li>
          </ul>

        </div>
        <div class='mFDiv mFoot2'>
          <h3>2. Получаю заказ</h3>
          <ul>
            <li>Мне доставляет курьер<br>
              <a href="<?php echo url_for('wordpress', array('page' => 'how_get_order',)) ?>">Посмотреть условия<br> доставки</a></li>
            <li>Бесплатно забираю сам<br>в одном из <?php echo $shopCount ?> магазинов<br>
              <a href="<?php echo url_for('shop') ?>">Найти Enter рядом со мной</a>
            </li>
          </ul>
        </div>
        <div class='mFDiv'>
          <h3>3. Оплачиваю заказ</h3>
          <ul>
            <li>Наличными<br>
              <a href="<?php echo url_for('wordpress', array('page' => 'how_pay',)) ?>">Банковской картой</a><br>
              (Visa, MasterCard и др.)<br>
              на сайте, курьеру<br>
              или в магазине
            </li>
            <li>
              <a href="<?php echo url_for('wordpress', array('page' => 'credit',)) ?>">Оформляю кредит</a>
            </li>
          </ul>
        </div>
      </dd>
      <dd class='bFooterF1'>
        <a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>">
          <h3>Заказываю услуги в сервисной Службе F1</h3>
          Мне собирают, подключают, настраивают и рекомендуют
        </a>
      </dd>
    </dl>
  </div>

  <div class='bFooterBottom'>
    Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.<br>
    <a href="<?php echo url_for('wordpress', array('page' => 'legal')) ?>">Правовая информация</a>
    <a href="<?php echo url_for('wordpress', array('page' => 'terms')) ?>">Условия продажи</a>
    <a href="<?php echo url_for('wordpress', array('page' => 'media_info')) ?>">Информация о СМИ</a><br/><br/>
    <span class="copyright">&copy; ООО &laquo;Энтер&raquo; 2011&ndash;<?php echo date("Y") ?>. ENTER<sup>&reg;</sup> ЕНТЕР<sup>&reg;</sup> <span style="color: #FF0000; font:normal 1em tahoma;">Enter<sup>&reg;</sup></span>. Все права защищены.</span>
    <div><a href="#" id="jira">Сообщить об ошибке</a></div>
  </div>
</div>
<!-- /Footer -->