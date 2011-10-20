<ul class="form checkboxlist pb15">
<?php $i = 0; foreach ($list as $item): $i++ ?>
  <li<?php echo ($i > 10) ? ' class="hf"' : ''?>>
    <label for="filter_product_type-<?php echo $item['token'] ?>"><?php echo $item['name'] ?> <?php echo "({$item['count']})" ?></label>
    <input id="filter_product_type-<?php echo $item['token'] ?>" name="product_types[]" type="checkbox" value="<?php echo $item['value'] ?>" <?php if ($item['selected']) echo 'checked="checked" ' ?>/>
  </li>
<?php endforeach ?>
<li><div id="plus10" class="more">еще + 10</div></li>
</ul>
