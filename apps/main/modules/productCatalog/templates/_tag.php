<h2>Выбираем <?php echo mb_lcfirst($productCategory) ?></h2>
<div class="line pb10"></div>


<!--div class="pb5"><a href="" class="underline">Показать все товары</a> <span class="font10 gray">(<?php echo $productCategory->countProduct() ?>)</span></div-->

<!-- Filter -->
<form class="product_filter-block" action="<?php echo $url ?>" method="get" data-action-count="<?php echo url_for('productCatalog_count', $sf_data->getRaw('productCategory')) ?>">

  <dl class="bigfilter form">
    <?php $i = 0; foreach ($form as $name => $field): if ($i++ > 7) break; ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>

      <dt<?php if (4 > $i) echo ' class="current'.((1 == $i) ? ' first' : '').'"' ?>><?php echo $form[$name]->renderLabelName() ?>
        <?php //include_partial('productCatalog/filter_hint')  ?>
      </dt>

      <dd<?php if (4 > $i) echo ' style="display: block"' ?>>
        <?php echo $form[$name]->render() ?>
      </dd>
<?php endforeach; ?>
  </dl>

  <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать" /></div>
  <!--div class="pb15"><a href="" class="button whitelink">Расширенный поиск</a></div-->
</form>

<!-- /Filter -->