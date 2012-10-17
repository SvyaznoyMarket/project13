<?php
/** @var $cart UserCartNew */
?>

<?php slot('title', 'Моя корзина') ?>

<div style="float:left; width: 100%; padding-bottom: 20px">
  <div id="adfox920" class="adfoxWrapper"></div>
</div>
<?php if ($cart->countFull() > 0): ?>
  <?php include_component('cart_', 'show') ?>

<div class="fl width345 font14">
  <?php if (($cart->getProductsPrice() >= ProductEntity::MIN_CREDIT_PRICE ) && sfConfig::get('app_payment_credit_enabled', true)) : ?>
  <div id="creditFlag" style="display:none">
    <label class="bigcheck <?php if ($selectCredit) echo 'checked'; ?>" for="selectCredit">
      <b></b>Выбранные товары купить в кредит
      <input autocomplete="off" class="" type="checkbox" id="selectCredit" <?php if ($selectCredit){ echo 'checked="checked"'; }?>
             name="selectCredit"/>
    </label>

    <div class="pl35 font11">
      Получение кредита происходит онлайн после оформления заказа на сайте: заполняете заявку на кредит в банк,
      и в течение нескольких минут получаете СМС о решении банка. Оригиналы документов мы привезем вместе с выбранными товарами!
    </div>
  </div>
  <?php endif; ?>

</div>
<div id="total" class="fr">
  <div class="left">
    <?php if (($cart->getProductsPrice() >= ProductEntity::MIN_CREDIT_PRICE ) && sfConfig::get('app_payment_credit_enabled', true)) : ?>
    <div id="creditSum" data-minsum="<?php echo ProductEntity::MIN_CREDIT_PRICE ?>" style="display:none">
      <div class="font14 width370 creditInfo pb10 grayUnderline">
        <div class="leftTitle">Сумма заказа:</div>
        <div class="font24">
          <span class="price"><?php echo $cart->getTotal(true) ?></span> <span class="rubl">p</span>
        </div>
      </div>
      <div style="display:none" id="blockFromCreditAgent">
        <div class="font14 width370 creditInfo pb10 pt10">
          <div class="leftTitle">
            <strong>Ежемесячный платеж<sup>*</sup>:</strong>
          </div>
          <div class="font24">
            <strong>
              <span id="creditPrice">(считаем...)</span>
              <span class="rubl">p</span>
            </strong>
          </div>
        </div>
        <div class="font11 width370"><sup>*</sup> Кредит не распространяется на услуги F1 и доставку.
          Сумма платежей предварительная и уточняется банком в процессе принятия кредитного решения.
        </div>
      </div>    
    </div>
    <?php endif; ?>

    <div id="commonSum">
      <div class="font14">
        Сумма заказа:
      </div>
      <div class="font30"><strong>
				<span class="price">
						<?php echo $cart->getTotal(true); ?>
				</span>
        <span class="rubl">p</span></strong></div>
    </div>
  </div>

</div>
<div class="clear pb25"></div>
<div class="line pb30"></div>
<div class="fl font14 pt10">&lt; <a class="underline" href="/">Вернуться к покупкам</a></div>
<div class="width500 auto">
  <a href="<?php echo url_for('order_new') ?>" class="bBigOrangeButton width345">Оформить заказ</a>
</div>
<div class="clear"></div>
<?php else: ?>
<p>в корзине нет товаров</p>

<?php endif ?>


<ul class="bBuyingFooter">
  <li>
    <div class="bBuyingFooter__eEnter"></div>
    <h3>Ответы на вопросы</h3>
    <span>Наш Контакт cENTER<br><b>8 (800) 700 00 09</b><br> 24 часа в сутки / 7 дней в неделю. Звонок бесплатный. Радость в подарок :)</span>
  </li>
  <li>

    <div class="bBuyingFooter__eZakaz"></div>
    <h3>Безопасные покупки</h3>
    <span>Вы приобретаете качественный товар. Получаете и оплачиваете любым удобным для Вас способом.</span>
  </li>
  <li>
    <div class="bBuyingFooter__ePeople"></div>
    <h3>Сопровождение заказа</h3>

    <span>После оформления заказа, с Вами свяжется специалист нашего Контакт cENTER для подтверждения заказа.</span>
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

<?php slot('seo_counters_advance') ?>
<!--  AdRiver code START. Type:counter(zeropixel) Site: sventer SZ: baskets PZ: 0 BN: 0 -->
<script language="javascript" type="text/javascript"><!--
var RndNum4NoCash = Math.round(Math.random() * 1000000000);
var ar_Tail='unknown'; if (document.referrer) ar_Tail = escape(document.referrer);
document.write('<img src="http://ad.adriver.ru/cgi-bin/rle.cgi?' + 'sid=179070&sz=baskets&bt=21&pz=0&rnd=' + RndNum4NoCash + '&tail256=' + ar_Tail + '" border=0 width=1 height=1>')
//--></script>
<noscript><img src="http://ad.adriver.ru/cgi-bin/rle.cgi?sid=179070&sz=baskets&bt=21&pz=0&rnd=1616108824" border=0 width=1 height=1></noscript>
<!--  AdRiver code END  -->

<div id="heiasOrder" data-vars="<?php echo $cart->getSeoCartArticle(); ?>" class="jsanalytics"></div>
<?php end_slot() ?>

<!--Трэкер "Корзина"-->
<script>document.write('<img src="http://mixmarket.biz/tr.plx?e=3779415&r='+escape(document.referrer)+'&t='+(new Date()).getTime()+'" width="1" height="1"/>');</script>
<!--Трэкер "Корзина"-->
