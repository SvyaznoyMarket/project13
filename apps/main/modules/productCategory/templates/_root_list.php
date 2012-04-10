<?php $i = 0; foreach ($list as $item):
    if (!isset($item['is_shown_in_menu'])) {
        $item['is_shown_in_menu'] = 1;
    }
    if ($item['is_shown_in_menu'] == 0) continue;
    $i++;
    if ($i > 13) break;
  ?>
  <a id="topmenu-root-<?php echo $item['root_id'] ?>" title="<?php echo $item['name'] ?>" alt="<?php echo $item['name'] ?>" class="bToplink" href="<?php echo url_for('productCatalog_category', array('productCategory' => $item['token_prefix'] ? ($item['token_prefix'].'/'.$item['token']) : $item['token'])) ?>">
    <span class="category-<?php echo $i ?>">
    </span>
  </a>
<?php endforeach ?>
