<!-- Footer -->
<div class="footer<?php if (isset($class)) echo " ".$class ?>">
  <div class="bottommenu">
    <?php if (has_slot('navigation_seo')) {
        include_slot('navigation_seo');
    }
    ?>
    <dl>
      <dt><a href="<?php echo url_for('default_show', array('page' => 'about_company',)) ?>"><strong>О компании</strong></a></dt>
      <dd>Наша миссия: дарить время для</dd>
      <dd>настоящего. Честно. С любовью.</dd>
      <dd>Как для себя.</dd>
    </dl>
    <dl>
      <dt><a href="<?php echo url_for('default_show', array('page' => 'how_make_order',)) ?>"><strong>Помощь покупателю</strong></a></dt>
      <dd><a href="<?php echo url_for('default_show', array('page' => 'how_make_order',)) ?>">Как сделать заказ?</a></dd>
      <dd><a href="<?php echo url_for('default_show', array('page' => 'how_get_order',)) ?>">Как получить заказ?</a></dd>
      <dd><a href="<?php echo url_for('default_show', array('page' => 'how_pay',)) ?>">Как оплатить заказ?</a></dd>
    </dl>
    <dl>
      <dt><a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>"><strong>F1 сервис</strong></a></dt>
      <dd><a href="<?php echo url_for('default_show', array('page' => 'f1',)) ?>"><img src="/images/enter/f1_footer.jpg" alt="F1 услуги" width="209" height="46"></img></a></dd>
    </dl>
    <dl class="next">
      <dt><strong>Круглосуточный контакт cENTER</strong></dt>
      <dd><strong class="font16">8 (800) 700 00 09</strong></dd>
      <dd>Звонок бесплатный.<br />Радость в подарок :)</dd>
    </dl>
  </div>
<?php if (false): ?>
  <div class="social">
    Посетите нас
    <a href="#" class="twitter">twitter</a>
    <a href="#" class="facebook">facebook</a>
    <a href="#" class="vkontakte">vkontakte</a>
    <a href="#" class="livejournal">livejournal</a>
    <a href="#" class="youtube">youtube</a>
    <a href="#" class="mobile">mobile</a>
  </div>

  <div class="subscribeform">
    <form action=""><input type="text" class="text" value="Введите Ваш E-mail" onfocus="if (this.value == 'Введите Ваш E-mail') this.value = '';" onblur="if (this.value == '') this.value = 'Введите Ваш E-mail';"  /><input type="button" class="subscribebutton" value="Подписаться на рассылку" /></form>
  </div>
<?php endif ?>
  <div class="footerbottom">
    <div class="copy">
      &copy; &laquo;Enter&raquo; 2011&ndash;<?php echo date("Y") ?>. Все права защищены.<br />Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.<br /><a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>">Условия продажи</a><a href="<?php echo url_for('default_show', array('page' => 'legal')) ?>">Правовая информация</a>
      <?php if (false): ?>
      &copy; &laquo;Enter&raquo; 2002-2011. Все права защищены.<br />
      Указанная стоимость товаров и условия их приобретения действительны по состоянию на текущую дату.

      <div>
        <a href="">Политика конфидециальности</a>
        <a href="<?php echo url_for('default_show', array('page' => 'terms')) ?>">Условия продажи в интернет-магазине</a>
        <b><i class="mistakeimg"></i><a href="" class="orange">сообщить!</a></b>
      </div>
      <?php endif ?>
    </div>

    <div class="social_network fr"><span class="gray font11">Посетите нас</span><?php include_component('default', 'social_networks') ?></div>

    <?php if (false): ?>
    <div class="counter">
      <a href=""><img src="/images/images/counter1.gif" alt="" width="80" height="30" /></a>
      <a href=""><img src="/images/images/counter2.gif" alt="" width="50" height="30" /></a>
      <a href=""><img src="/images/images/counter3.gif" alt="" width="50" height="30" /></a>
    </div>
    <?php endif ?>
  </div>
</div>
<!-- /Footer -->