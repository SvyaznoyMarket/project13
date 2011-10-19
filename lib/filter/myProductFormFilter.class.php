<?php

class myProductFormFilter extends sfFormFilter
{
  public function configure()
  {
    parent::configure();

    $this->setOption('mark_required', false);

    $this->disableCSRFProtection();

    if (!$productCategory = $this->getOption('productCategory'))
    {
      throw new InvalidArgumentException('You must provide a productCategory object.');
    }
    $creator = $this->getOption('creator', null);

    $productTable = ProductTable::getInstance();

    // виджет цены
    $this->widgetSchema['price'] = $this->getWidgetRange(null, array(
      'from' => (int)$productTable->getMinPriceByCategory($productCategory),
      'to'   => (int)$productTable->getMaxPriceByCategory($productCategory),
    ));
    $this->widgetSchema['price']->setLabel('Цена');
    $this->validatorSchema['price'] = new sfValidatorPass();

    // виджет производителя
    $choices = CreatorTable::getInstance()
      ->getListByProductCategory($productCategory, array('select' => 'creator.id, creator.name'))
      ->toKeyValueArray('id', 'name')
    ;
    $this->widgetSchema['creator'] = new myWidgetFormChoice(array(
      'choices'          => $choices,
      'multiple'         => true,
      'expanded'         => true,
      'renderer_class'   => 'myWidgetFormSelectCheckbox',
      'renderer_options' => array(
        'formatter'       => array($this, 'show_part'),
        'label_separator' => '',
      ),
    ));
    $this->widgetSchema['creator']->setLabel('Производитель');
    $this->widgetSchema['creator']->setDefault($creator ? $creator->id : null);
    $this->validatorSchema['creator'] = new sfValidatorPass();

    // виджеты параметров
    $filters = $this->getOption('count', false) ? $productCategory->FilterGroup->Filter : $productCategory->getFilterGroupForFilter();

    foreach ($filters as $productFilter)
    {
      if (!count($productFilter->Property->Option)) continue;

      if (!$widget = call_user_func(array($this, 'getWidget'.sfInflector::camelize($productFilter->type)), $productFilter)) continue;

      $index = "param-{$productFilter->id}";
      $this->setWidget($index, $widget);
      $this->setValidator($index, new sfValidatorPass());
      $this->widgetSchema[$index]->setLabel($productFilter->name);
    }

    $this->widgetSchema->setNameFormat('f[%s]');
  }

  public function buildQuery(myDoctrineQuery $q)
  {
    $productCategory = $this->getOption('productCategory');

    $filter = array(
      'category'   => $productCategory,
      'creator'    => $this->values['creator'],
      'price'      => array(
        'from' => $this->values['price']['from'],
        'to'   => $this->values['price']['to'],
      ),
      'parameters' => array(),
    );

    $productFilterList = $productCategory->FilterGroup->Filter;
    $productFilterList->indexBy('id');
    foreach ($this->values as $id => $param)
    {
      if (0 !== strpos($id, 'param-')) continue;

      $productFilter = $productFilterList->getByIndex('id', substr($id, 6));
      if (!$productFilter) continue;

      $filter['parameters'][] = array(
        'filter' => $productFilter,
        'values' => $param,
      );
    }

    ProductTable::getInstance()->setQueryForFilter($q, $filter);
  }

  protected function getWidgetChoice($productFilter)
  {
    $choices = array();
    foreach ($productFilter->Property->Option as $productPropertyOption)
    {
      $choices[$productPropertyOption->id] = $productPropertyOption->value;
    }

    return new myWidgetFormChoice(array(
      'choices'          => $choices,
      'multiple'         => $productFilter->is_multiple,
      'expanded'         => true,
      'renderer_class'   => 'myWidgetFormSelectCheckbox',
      'renderer_options' => array(
        'label_separator' => '',
      ),
    ));
  }

  protected function getWidgetRange(ProductFilter $productFilter = null, array $value)
  {
    return new myWidgetFormRange(array(
      'value_from' => $value['from'],
      'value_to'   => $value['to'],
      'template'   => ''
        .'<div class="pb5"><input type="text" style="height:10px; padding:0; line-height:10px; border:0; background:none; font-size:10px; color:#8a8a8a"  disabled="disabled" /> %value_from% %value_to%</div>'
        .'<div class="sliderbox">'
          .'<div id="slider-range1" class="slider-range"></div>'
          .'<span class="fl">'.$value['from'].'</span>'
          .'<span class="fr">'.$value['to'].'</span>'
        .'</div>'
        .'<div class="clear"></div>'
    ));
  }

  public function show_part($widget, $inputs)
  {
    $rows = array();
    $shown = array_slice($inputs, 0, 5);
    foreach ($shown as $input)
    {
      $rows[] = $widget->renderContentTag('li', $input['input'].$widget->getOption('label_separator').$input['label']);
    }
    if (count($inputs) > 5)
    {
      $hidden = array_slice($inputs, 5);
      foreach ($hidden as $input)
      {
        $rows[] = $widget->renderContentTag('li', $input['input'].$widget->getOption('label_separator').$input['label'], array('class' => 'hf', 'style' => 'display: none', ));
      }
      $rows[] = $widget->renderContentTag('li', 'еще...', array('class' => 'fm', 'style' => 'text-align: right;'));
    }

    return !$rows ? '' : $widget->renderContentTag('ul', implode($widget->getOption('separator'), $rows), array('class' => $widget->getOption('class')));
  }
}