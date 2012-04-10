<?php
//если для текущего продаукта это свойства не задано, считаем
//фильтр моделей по нему некорректным
if (!isset($property['current'])) {
    return;
}
?>
<div class="bDropWrap">
  <h5><?php echo $property['name'] ?>:</h5>
  <div class="bDropMenu">
    <span class="bold"><a href="<?php echo $property['current']['url'] ?>"><?php echo $property['current']['value'] ?></a></span>

    <div>
      <span class="bold"><a href="<?php echo $property['current']['url'] ?>"><?php echo $property['current']['value'] ?></a></span>

    <?php foreach ($property['products'] as $product):
        if ($property['current']['id'] == $product['id']) {
            continue;
        }
        ?>
        <span>
            <a href="<?php echo $product['url'] ?>">
                <?php echo $product['value'] ?>
            </a>
        </span>
      <?php endforeach ?>
    </div>

  </div>
</div>
