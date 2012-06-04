<?php
/**
 * @var $item ProductEntity
 */
?>
<?php foreach ($item->getPropertyGroupList() as $group){ ?>
  <?php if(count($group->getAttributeList())==0) continue; ?>
  <div class="pb15"><strong><?php echo $group->getName() ?></strong></div>
  <?php foreach ($group->getAttributeList() as $attr){ ?>
    <div class="point">
      <div class="title"><h3><?php echo $attr->getName() ?></h3></div>
      <div class="description">
        <?php echo $attr->getStringValue() ?>
      </div>
    </div>
  <?php } ?>
<?php } ?>
