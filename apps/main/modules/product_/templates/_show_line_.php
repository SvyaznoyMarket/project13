<?php
/**
 * @var ProductEntity $item
 * @todo line count!
 */
if(!$item->getLine() || !$item->getLine()->getToken())
  return;  // @todo do not view if not line token exists
?>
<div class="goodsbox height250"<?php echo (isset($ii) && $ii > 3) ? ' style="display:none;"' : '' ?>>
  <div class="photo">
    <a href="<?php echo $item->getLine()->getLink() ?>">
      <?php if ($label = $item->getMainLabel()): ?>
      <img class="bLabels" src="<?php echo $label->getImageUrl() ?>" alt="<?php echo $label->getName() ?>"/>
      <?php endif; ?>
      <img src="<?php echo $item->getMediaImageUrl() ?>"
           alt="Серия <?php echo $item->getLine()->getName() ?>"
           title="Серия <?php echo $item->getLine()->getName() ?>"
           width="160" height="160"/>
    </a>
  </div>
  <h3>
    <a href="<?php echo $item->getLine()->getLink() ?>">
      <strong>Серия <?php echo $item->getLine()->getName() ?></strong>
      <span class="font10 gray"> (<?php echo $item->getLine()->getTotalCount() ?>)</span>
    </a>
  </h3>

  <!-- Hover -->
  <div class="boxhover"<?php if ($item->getIsBuyable()): ?> ref="<?php echo $item->getToken() ?>"<?php endif ?>>
    <b class="rt"></b><b class="lb"></b>

    <div class="rb">
      <div class="lt" data-url="<?php echo $item->getLine()->getLink() ?>">
        <!--a href="" class="fastview">Быстрый просмотр</a-->

        <div class="photo"><!--<i class="new" title="Новинка"></i>-->
          <a href="<?php echo $item->getLine()->getLink() ?>">
            <?php if ($label = $item->getMainLabel()): ?>
            <img class="bLabels" src="<?php echo $label->getImageUrl() ?>" alt="<?php echo $label->getName() ?>"/>
            <?php endif; ?>
            <img src="<?php echo $item->getMediaImageUrl() ?>"
                 alt="Серия <?php echo $item->getLine()->getName() ?>"
                 title="Серия <?php echo $item->getLine()->getName() ?>"
                 width="160"
                 height="160"/>
          </a>
        </div>
        <h3>
          <a href="<?php echo $item->getLine()->getLink() ?>">
            <strong>Серия <?php echo $item->getLine()->getName() ?></strong>
            <span class="font10 gray"> (<?php echo $item->getLine()->getTotalCount() ?>)</span>
          </a>
        </h3>
      </div>
    </div>
  </div>
  <!-- /Hover -->

</div>