<?php
/**
 * @var ServiceEntity $service
 */
$json = array(
    'jsref' => $service->getToken(),
    'jsimg' => $service->getMediaImage() ? $service->getMediaImageUrl(2) : '/images/f1infobig.png',
    'jstitle' => $service->getName(),
    'jsprice' => number_format($service->getPrice(), 0, ',', ' '),
);

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
                <span class="price"><?php echo number_format($service->getPrice(), 0, ',', ' ') ?></span>
                <span class="rubl">p</span>
                <?php else: ?>
                <span class="price">бесплатно</span>
                <?php endif ?>
            </strong>
        </div>
        <?php endif ?>
        <?php if($service->getIsDelivery()): ?>
        <a class="link1 gaEvent" href="<?php echo $this->url('cart.addService', array('serviceId' => $service->getId(), 'quantity' => 1)); ?>" data-event="BuyF1" data-title="Заказ услуги F1">Купить услугу</a>
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



<?php
/**
 *  @var $service ServiceEntity
 */
?>
<?php if(count($service->getAlikeList())): ?>
    <div class="rubrictitle"><h3>А так же есть похожие услуги:</h3></div>
    <div class="line pb15"></div>
    <div class="clear"></div>

    <div class="bServiceCardWrap">

        <?php foreach ($service->getAlikeList() as $alike): ?>
        <div class="bServiceCard mInlineBlock" ref="<?php echo $alike->getToken() ?>">
            <div class="bServiceCard__eImage">
                <a href="<?php echo $this->url('service.show', array('service' => $alike->getToken())) ?>">
                    <?php if ($alike->getMediaImage()): ?>
                    <img src="<?php echo $alike->getMediaImageUrl(2); ?>" alt="<?$alike->getName()?>"/>
                    <?php endif ?>
                    <div class="bServiceCard__eLogo_free"></div>
                </a>
            </div>

            <p class="bServiceCard__eDescription mb5">
                <a href="<?php echo $this->url('service.show', array('service' => $alike->getToken())) ?>">
                    <?php echo $alike->getName(); ?>
                </a>
            </p>

            <div class="bServiceCard__ePrice pb10">
                <?php if($alike->getPrice()): ?>
                <?php echo number_format($alike->getPrice(), 0, ',', ' '); ?>
                <span class="rubl">p</span>
                <?php elseif ($alike->getIsInShop()): ?>
                <div>доступна в магазине</div>
                <?php endif ?>
            </div>

            <?php $json = array(
            'jsref' => $alike->getToken(),
            'jsimg' => $alike->getMediaImageUrl(),
            'jstitle' => $alike->getName(),
            'jsprice' => number_format($alike->getPrice(), 0, ',', ' '),
            'url' => $this->url('cart.addService', array('serviceId' => $alike->getId(), 'quantity' => 1)),
        ) ?>

            <?php if ($alike->getIsInShop()) : ?>
            <form action="<?php echo $this->url('cart.addService', array('serviceId' => $alike->getId(), 'quantity' => 1)) ?>"/>
            <input data-value='<?php echo json_encode($json) ?>' data-event="BuyF1" data-title="Заказ услуги F1" type="submit"  class="button yellowbutton gaEvent" value="Купить услугу"/>
            </form>
            <?php endif ?>
        </div>
        <?php endforeach ?>
    </div>
<?php endif ?>