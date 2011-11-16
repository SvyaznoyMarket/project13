<?php if (false): ?>
<h2>Выбираем <?php echo mb_lcfirst($productCategory) ?></h2>
<div class="line pb10"></div>
<?php endif ?>

<!--div class="pb5"><a href="" class="underline">Показать все товары</a> <span class="font10 gray">(<?php echo $productCategory->countProduct() ?>)</span></div-->

<!-- Filter -->
<form class="product_filter-block" action="<?php echo $url ?>" method="get" data-action-count="<?php echo url_for('productCatalog_count', $sf_data->getRaw('productCategory')) ?>">

  <dl class="bigfilter form bSpec">
    <h2>Выбираем:</h2>  
    <?php include_component('productCatalog', 'tag_selected', array('form' => $form, 'productCategory' => $productCategory)) ?>
    <?php $i = 0; foreach ($form as $name => $field): if ($i > 7) break; ?>
    <?php //echo $name; ?>
      <?php if ((isset($form[$name]) && $form[$name]->isHidden()) || (!isset($form[$name]) && $field->isReal())) continue ?>
      <?php $i++ ?>

      <dt<?php //if (4 > $i) echo ' class="'.((1 == $i) ? ' first' : '').'"' ?>><?php echo $form[$name]->renderLabelName() ?>
        <?php //include_partial('productCatalog/filter_hint')  ?>
      </dt>

      <?php
        if ($name == 'price' || $name == 'creator'){
            $open = 'block';
        } else {
            $open = 'none';
        }
        /*
            //DEPRICATED открывать выбранные
            if (get_class( $field->getWidget() ) == 'myWidgetFormRange'){
                $info = $field->getWidget()->getOptions();
                $currentVal = $form[$name]->getValue();
                if ($info['value_from'] != $currentVal['from'] || $info['value_to'] != $currentVal['to']){
                    $open = 'block';
                } else {
                    $open = 'none';
                }
            } else {
                if (count($form[$name]->getValue())>0){
                    $open = 'block';
                } else {
                    $open = 'none';                
                }
            }
         * 
         */
      ?>
      <dd style="display: <?php echo $open ?>">
        <?php echo $form[$name]->render() ?>
      </dd>
    <?php endforeach ?>
    <div class="pb10"><input type="submit" class="button yellowbutton" value="Подобрать" /></div>
  </dl>

  <!--div class="pb15"><a href="" class="button whitelink">Расширенный поиск</a></div-->
</form>

<!-- /Filter -->