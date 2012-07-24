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
          <a href="<?php echo url_for('service_show', array('service' => $alike->getToken())) ?>">
          <?php if ($alike->getMediaImage()): ?>
              <img src="<?php echo $alike->getMediaImageUrl(2); ?>" alt="<?$alike->getName()?>"/>
          <?php endif ?>
              <div class="bServiceCard__eLogo_free"></div>
          </a>
        </div>

        <p class="bServiceCard__eDescription mb5">
          <a href="<?php echo url_for('service_show', array('service' => $alike->getToken())) ?>">
            <?php echo $alike->getName(); ?>
          </a>
        </p>

        <div class="bServiceCard__ePrice pb10">
          <?php if($alike->getPrice()): ?>
            <?php echo formatPrice($alike->getPrice()); ?>
            <span class="rubl">p</span>
          <?php elseif ($alike->getIsInShop()): ?>
            <div>доступна в магазине</div>
          <?php endif ?>
        </div>

        <?php $json = array(
        'jsref' => $alike->getToken(),
        'jsimg' => $alike->getMediaImageUrl(),
        'jstitle' => $alike->getName(),
        'jsprice' => formatPrice($alike->getPrice()),
        'url' => url_for('cart_service_add', array('service' => $alike->getId())),
      ) ?>

        <?php if ($alike->getIsInShop() || $alike->getIsDelivery()) : ?>
          <pre><?php print_r($_SERVER)?></pre>
          <form action="<?php echo url_for('cart_service_add', array('service' => $alike->getId())) ?>"/>
            <input data-value='<?php echo json_encode($json) ?>' type="submit" class="button yellowbutton" value="Купить услугу"/>
          </form>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>
<?php endif ?>