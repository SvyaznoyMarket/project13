<?php
namespace light;

/**
 * @var $order   OrderData
 * @var $product ProductData
 * @var $productQuantity int
 * @var $region  RegionData
 * @var $shopName string
 * @var $this    HtmlRenderer
 */

require_once(Config::get('helperPath').'Counters.php');
require_once(Config::get('helperPath').'DateFormatter.php');


$list = $product->getCategoryList();
$rootCategory = $list[0];
$analyticsCategoryName = $rootCategory->getName() . (($product->getMainCategory()->getId() == $rootCategory->getId())? '' : $product->getMainCategory()->getName());

?>


<?php //$rememberMe = !$sf_user->isAuthenticated() && $form->isValid() ?>

<div style="width: 900px;">

  <div class="bFormSave">
    <h2>Номер вашего заказа: <?php echo $order->getNumber() ?></h2>

    <p>Дата заказа: <?php echo DateFormatter::Humanize(new \DateTime()) ?>.<br>Сумма
      заказа: <?php echo number_format($order->getTotalPrice(), 0, ',', ' ') ?> <span class="rubl">p</span></p>
    <span>В ближайшее время мы свяжемся с вами для уточнения параметров заказа.</span>
  </div>

  <?php if ($order->getNumber()): ?>

  <?php echo Counters::getBlock('order1ClickSuccess'); ?>

  <script type="text/javascript">
    function runAnalitics() {
      if (typeof(_gaq) !== 'undefined') {
        _gaq.push(['_addTrans',
          '<?php echo $order->getNumber() . '_F' ?>', // Номер заказа
          '<?php echo $shopName ?>', // Название магазина (Необязательно)
          '<?php echo str_replace(',', '.', $order->getTotalPrice()) ?>', // Полная сумма заказа (дроби через точку)
          '0', // Стоимость доставки (дроби через точку)
          '<?php echo $region->getName() ?>', // Город доставки (Необязательно)
          '<?php //echo $order->getAreaName() ?>', // Область (необязательно)
          '<?php //echo $order->getCountryName() ?>'             // Страна (нобязательно)
        ]);
        _gaq.push(['_trackEvent', 'QuickOrder', 'Success']);
          _gaq.push(['_addItem',
            '<?php echo $order->getNumber() . '_F' ?>', // Номер заказа
            '<?php echo $product->getArticle() ?>', // Артикул
            '<?php echo $product->getName() ?>', // Название товара
            '<?php echo $analyticsCategoryName ?>', // Категория товара
            '<?php echo str_replace(',', '.', $product->getPrice()) ?>', // Стоимость 1 единицы товара
            '<?php echo str_replace(',', '.', $productQuantity) ?>'               // Количество товара
          ]);
        _gaq.push(['_trackTrans']);
      }

      var yaParams = {
        order_id:'<?php echo $order->getNumber() ?>',
        order_price: <?php echo str_replace(',', '.', $order->getTotalPrice()) ?>,
        currency:'RUR',
        exchange_rate:1,
        goods:[
            {
              id:'<?php echo $product->getArticle() ?>',
              name:'<?php echo $product->getName() ?>',
              price: <?php echo str_replace(',', '.', $product->getPrice()) ?>,
              quantity: <?php echo $productQuantity ?>
            }
        ]
      };
      if (typeof(yaCounter10503055) !== 'undefined')  yaCounter10503055.reachGoal('QORDER', yaParams);

      if (typeof(window.adBelnder) != 'undefined') window.adBelnder.addOrder(<?php echo str_replace(',', '.', $order->getTotalPrice()) ?>);
    }
  </script>

  <?php endif ?>

  <div class="line"></div>

  <div class="bFormB2">
    <div class="fr">
      <a href="<?php echo $this->url('staticPage.mainPage') ?>" onclick="$('#order1click-container-new').trigger('close'); return false">Продолжить
        покупки</a> <span>&gt;</span>
    </div>
  </div>

</div>