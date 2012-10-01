<?php
namespace light;
/**
 * @var $Menu mainMenuData
 */

foreach($Menu as $Category){ ?>
<div class="extramenu" style="display: none;" id="extramenu-root-<?php echo $Category->getId(); ?>">
  <i class="corner" style="left:290px"></i>
  <span class="close" href="#"></span>
  <?php for($i=0; $i< 4; $i++){ $block = $Menu->getBlock($i); ?>
  <dl>
    <?php foreach( $block as $category){ ?>
    <dt><a href="<?php echo $category['link'] ?>"><?php echo $category['name'] ?></a></dt>
    <?php foreach( $category['children'] as $subCategory){ ?>
      <dd><a href="<?php echo $subCategory['link'] ?>"><?php echo $subCategory['name'] ?></a></dd>
      <?php } ?>
    <?php } ?>
  </dl>
  <?php } ?>
  <div class="clear"></div>
</div>
<?php } ?>

<!--light version-->