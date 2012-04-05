<?php
if (!$currentCat || !$list) {
  return;
}
if (!isset($currentDirectory)) $currentDirectory = array();
?>
<div class="catProductNum"><b>Всего <?php echo $quantity . ($currentCat->has_line ? ' серий' : ' товаров') ?></b></div>
<div class="line pb10"></div>
<dl class="bCtg">


  <dd>
    <ul>
      <?php
      foreach ($list as $key => $item) {
        ?>
        <?php
        if (!in_array($item['id'], $notFreeCatList)) {
          continue;
        }
        ?>
        <li class="bCtg__eL<?php echo $item['level'] + 1;
          if ($currentCat->level == 0 && $currentCat->id == $item['id']) echo " hidden";
          elseif (is_array($pathAr) && in_array($item['id'], $pathAr)) echo " mBold";
          elseif ($currentCat->id == $item['id']) echo " mSelected";
          elseif ($hasChildren && $item['core_parent_id'] == $currentCat->core_id) echo '';
          elseif (!$hasChildren && $item['core_parent_id'] == $currentCat->core_parent_id) echo '';
          else echo ' hidden';
          ?> ">
          <a
            href="<?php echo url_for('productCatalog__category', array('productCategory' => $item['token_prefix'] ? ($item['token_prefix'] . '/' . $item['token']) : $item['token'])); ?>">
            <span><?php echo $item['name'] ?></span>
          </a>
        </li>
        <?php
      }
      ?>
    </ul>
  </dd>

</dl>

