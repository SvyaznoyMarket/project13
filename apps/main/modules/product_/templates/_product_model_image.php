<?php
/**
 * @var $item ProductEntity
 * @var $property ProductPropertyEntity
 */
?>
<div class="bDropWrap">
  <h5><?php echo $property->getName() ?>:</h5>

  <ul class="previewlist">
    <?php foreach ($property->getOptionList() as $option): ?>
    <li>
      <b<?php echo ($item->getId() == $option->getProduct()->getId()) ? ' class="current"' : '' ?> title="<?php echo $option->getHumanizedName() ?>"><a
        href="<?php echo $option->getProduct()->getLink() ?>"></a></b>
      <img src="<?php echo $option->getProduct()->getMediaImageUrl(1) ?>" alt="<?php echo $option->getHumanizedName() ?>" width="48" height="48"/>
    </li>
    <?php endforeach ?>
  </ul>
</div>

<div class="clear"></div>
