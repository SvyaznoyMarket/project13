<div class="bSpecSel">
  <h3>Ваш выбор:</h3>

  <ul>
    <?php foreach ($list as $item): ?>
      <li>
        <a href="<?php echo $item['url'] ?>" title="<?php echo $item['title'] ?>"><b>x</b> <?php echo $item['name'] ?></a>
      </li>
    <?php endforeach ?>
  </ul>

  <a class="bSpecSel__eReset" href="<?php echo url_for('productCatalog_category', $sf_data->getRaw('productCategory')) ?>">сбросить все</a>

</div>