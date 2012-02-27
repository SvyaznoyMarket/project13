<div class="bDropWrap">
  <h5><?php echo $property['name'] ?>:</h5>

  <ul class="previewlist">
  <?php foreach ($property['products'] as $product): ?>
      <?php //print_r($product); ?>
      <li>
          <b<?php echo ($product['is_selected']) ? ' class="current"' : '' ?> title="<?php echo $product['value'] ?>"><a href="<?php echo $product['url'] ?>"></a></b>
          <img src="<?php echo $product['image'] ?>" alt="<?php echo $product['value'] ?>" width="48" height="48" />
      </li>
  <?php endforeach ?>
  </ul>
</div>

<div class="clear"></div>
