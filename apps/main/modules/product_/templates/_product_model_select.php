<?php
/**
 * @var $item ProductEntity
 * @var $property ProductPropertyEntity
 */
?>
<?php
$productAttribute = $item->getAttribute($property->getId());
if (!$productAttribute) {
  return;
}
?>
<div class="bDropWrap">
  <h5><?php echo $property->getName() ?>:</h5>

  <div class="bDropMenu">
    <span class="bold"><a href="<?php echo $item->getLink() ?>"><?php echo $productAttribute->getStringValue() ?></a></span>

    <div>
      <span class="bold"><a href="<?php echo $item->getLink() ?>"><?php echo $productAttribute->getStringValue() ?></a></span>

      <?php foreach ($property->getOptionList() as $option):?>
      <!-- <?php print_r($productAttribute) ?> -->
      <?php if ($option->getName() == $productAttribute->getValue())continue; ?>
        <span>
          <a href="<?php echo $option->getProduct()->getLink() ?>">
            <?php echo $option->getHumanizedName() ?>
          </a>
        </span>
      <?php endforeach ?>
    </div>

  </div>
</div>
