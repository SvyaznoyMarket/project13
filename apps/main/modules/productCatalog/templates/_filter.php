<?php if (false): ?>
<h2>Выбираем <?php echo mb_lcfirst($productCategory) ?></h2>
<div class="line pb10"></div>
<?php endif ?>

<!--div class="pb5"><a href="" class="underline">Показать все товары</a> <span class="font10 gray">(<?php echo $productCategory->countProduct() ?>)</span></div-->


<!-- Filter -->
<form class="product_filter-block" action="<?php echo $url ?>" method="get" data-action-count="<?php echo url_for('productCatalog_count', $sf_data->getRaw('productCategory')) ?>">
  <?php echo $form->renderHiddenFields() ?>
  <dl class="bigfilter form bSpec">
    <h2>Enterесный выбор</h2>
    
    <?php include_component('productCatalog', 'filter_selected', array('form' => $form, 'productCategory' => $productCategory)) ?>

    <?php $i = 0; foreach ($form as $name => $field):  if ($i++ > 7) break;  ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>

      <dt<?php if (5 > $i) echo ' class="'.((1 == $i) ? ' first' : '').'"' ?>>
        <?php echo $form[$name]->renderLabelName() ?>
        <?php //include_partial('productCatalog/filter_hint')  ?>
      </dt>

      <dd style="display: none;">

        <?php if ($form[$name]->getWidget() instanceof myWidgetFormInputCheckbox): ?>
        <ul>
          <li>
          <?php echo $form[$name]->render() ?>
          <label for="<?php echo $form[$name]->renderId() ?>">да</label>
          </li>
        </ul>

        <?php else: ?>
          <?php echo $form[$name]->render() ?>
        <?php endif ?>

      </dd>
    <?php endforeach; ?>
    <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать" /></div>
  </dl>

  <!--div class="pb15"><a href="" class="button whitelink">Расширенный поиск</a></div-->
</form>

<!-- /Filter -->