<form id="filter_product_type-form" action="<?php echo $url ?>" method="post">
  <ul class="form checkboxlist pb15">
  <?php $i = 0; foreach ($list as $item): $i++ ?>
    <li>
      <label for="filter_product_type-<?php echo $i ?>"><?php echo $item['record'] ?> <?php echo "({$item['count']})" ?></label>
      <input id="filter_product_type-<?php echo $i ?>" name="product_types[]" type="checkbox" value="<?php echo $item['record']['id'] ?>" <?php if ($item['selected']) echo 'checked="checked" ' ?>/>
    </li>
  <?php endforeach ?>
  </ul>
</form>