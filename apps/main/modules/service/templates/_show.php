<?php
/**
 * @var ServiceEntity $service
 */
$json = array(
  'jsref' => $service->getToken(),
  'jsimg' => $service->getMediaImageUrl(2),
  'jstitle' => $service->getName(),
  'jsprice' => formatPrice($service->getPrice()),
)
?> 
<div class="bSet">

  <?php if ($service->getMediaImage()) { ?>
    <div class="bSet__eImage mServiceBig">
      <div class="bServiceCard__eLogo"></div>
      <img src="<?php echo $service->getMediaImageUrl(); ?>"/>
    </div>
    <div class="bSet__eInfo">
  <?php } else { ?>
    <div class="bSet__eImage_small mServiceBig">
      <img alt="" src="/images/f1infobig.png">
    </div>
    <div class="bSet__eInfo_big">
  <?php } ?>

  <p class="bSet__eDescription">
    <?php echo $service->getDescription() ?>
    <?php echo $service->getWork(); ?>
  </p>

  <div class="bSet__ePrice mServ" ref="<?php echo $service->getToken() ?>" data-value='<?php echo json_encode($json) ?>'>
    <?php if (!is_null($service->getPrice())): ?>
      <div class="font34">
        <strong>
          <?php if($service->getPrice()): ?>
            <span class="price"><?php echo formatPrice($service->getPrice()) ?></span>
            <span class="rubl">p</span>
          <?php else: ?>
            <span class="price">бесплатно</span>
          <?php endif ?>
        </strong>
      </div>
    <?php endif ?>

    <?php if ($service->isInsale() && $service->getIsDelivery()): ?>
      <a class="link1" href="<?php echo url_for('cart_service_add', array('service' => $service->getId())); ?>">Купить услугу</a>
    <?php elseif($service->getIsInShop()): ?>
      <b>Доступна в магазине</b>
      <p class="font14">Специалисты Контакт-сENTER с радостью проконсультируют по данной услуге и подскажут ближайший магазин Enter</p>
      <p class="font14">
        <strong>8 (800) 700 00 09</strong><br/>
        Skype: skype2enter и call2enter<br/>
        ICQ: 648198963
      </p>
    <?php endif ?>
  </div>
</div>
</div>

