<!-- Footer -->
<div class="footer<?php if (isset($class)) echo " ".$class ?>">
  <div class="bFooter">
    <div class='bFooter__eLeft'>
      <div class='bFooter__eLogo'></div>
      <a href="<?php echo url_for('default_show', array('page' => 'about_company',)) ?>">О Компании</a><br>
      <a href="<?php echo url_for('shop') ?>">Наши магазины</a><br>
      <a href="http://job.enter.ru/" target="_blank">Работать у нас</a><br>
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
            <li>в Контакт cEnter<br>
              <b class='mFphone'>8 (800) 700 00 09</b><br>
              <div class='bBottommenu__eSkype'><i>Skype</i><b>skype2enter и call2enter</b></div>
              <div class='bBottommenu__eICQ'><i>ICQ</i><b>648198963</b></div>
            </li>
            <li>Через сайт или по каталогу</li>
            <li>Терминалы в магазине</li>
            <li><a href="<?php echo url_for('default_show', array('page' => 'mobile_apps')) ?>">Мобильные и социальные приложения</a></li>
          </ul>
        </div>
        <div class='mFDiv mFoot2'>
          <h3>2. Получаю заказ</h3>
          <ul>
            <li>Мне доставляет курьер<br>
              <a href="<?php echo url_for('default_show', array('page' => 'how_get_order',)) ?>">Посмотреть условия<br> доставки</a></li>
            <li>Бесплатно забираю сам<br>в одном из 9 магазинов<br>
              <a href="<?php echo url_for('shop') ?>">Найти Enter рядом со мной</a>
            </li>
          </ul>
        </div>
        <div class='mFDiv'>
          <h3>3. Оплачиваю заказ</h3>
          <ul>
            <li>Наличными<br>
              <a href="<?php echo url_for('default_show', array('page' => 'how_pay',)) ?>">Банковской картой</a><br>
              (Visa, MasterCard и др.)<br>
              на сайте, курьеру<br>
              или в магазне
            </li>
            <li>
              <a href="<?php echo url_for('default_show', array('page' => 'credit',)) ?>">Оформляю кредит</a>
            </li>
          </ul>
        </div>
      </dd>
      <dd class='bFooterF1'>
        <a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>">
          <h3>Заказывают услуги в сервисной Службе F1</h3>
          Мне собирают, подключают, настраивают и рекомендуют
        </a>
      </dd>
    </dl>
  </div>

  <div class='bFooterBottom'>
    &copy; &laquo;Enter&raquo; 2002-2011. Все права защищены. Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.<br>
    <a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>">Правовая информация</a>
    <a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>">Условия продажи</a>
    <?php if (has_slot('navigation_seo')) {
    include_slot('navigation_seo');
  } ?>
  </div>
</div>
<!-- /Footer -->