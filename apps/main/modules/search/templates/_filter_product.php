<form id="filter_product_type-form" action="<?php echo $url ?>" method="post">
  <ul class="form checkboxlist pb15">
  <?php $i = 0; foreach ($list as $item): $i++ ?>
    <li<?php echo ($i > 10) ? ' class="hf"' : ''?>>
      <label for="filter_product_type-<?php echo $i ?>"><?php echo $item['record'] ?> <?php echo "({$item['count']})" ?></label>
      <input id="filter_product_type-<?php echo $i ?>" name="product_types[]" type="checkbox" value="<?php echo $item['record']['id'] ?>" <?php if ($item['selected']) echo 'checked="checked" ' ?>/>
    </li>
  <?php endforeach ?>
  <li><div id="plus10" class="more">еще + 10</div></li>
  </ul>
</form>