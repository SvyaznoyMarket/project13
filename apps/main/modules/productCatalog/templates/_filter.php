<form class="product_filter-block" action="<?php echo $url ?>" method="get" data-action-count="<?php echo url_for('productCatalog_count', $sf_data->getRaw('productCategory')) ?>">

  <dl class="bigfilter form">
    <?php foreach ($form as $name => $field): ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>

      <dt><?php echo $form[$name]->renderLabelName() ?><b></b>
        <?php //include_partial('productCatalog/filter_hint') ?>
      </dt>
      
      <dd>
        <?php echo $form[$name]->render() ?>
      </dd>
<?php endforeach; ?>
  </dl>

  <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать" /></div>
  <div class="pb15"><a href="" class="button whitelink">Расширенный поиск</a></div>
</form>