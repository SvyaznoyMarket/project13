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
    $productType = $this->getOption('productType', null);

    $productTable = ProductTable::getInstance();

    // виджет цены
    $valueMin = (int)$productTable->getMinPriceByCategory($productCategory);
    $valueMax = (int)$productTable->getMaxPriceByCategory($productCategory);
    $value = array(
      'min' => $valueMin,
      'max' => $valueMax,
    );
    $this->widgetSchema['price'] = $this->getWidgetRange(array('value_min' => $valueMin, 'value_max' => $valueMax), array(
      'from' => $value['min'],
      'to'   => $value['max'],
    ));
    $this->widgetSchema['price']->setLabel('Цена');
    $this->validatorSchema['price'] = new sfValidatorPass();
    $this->setDefault('price', array(
      'from' => $value['min'],
      'to'   => $value['max'],
    ));

    // виджет производителя
    $choices = CreatorTable::getInstance()
      ->getListByProductCategory($productCategory, array('select' => 'creator.id, creator.name', 'for_filter' => true, 'order' => 'creator.name'))
      ->toKeyValueArray('id', 'name')
    ;

    if (count($choices))
    {
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
    }
    // виджеты параметров
    $productFilterList = $this->getOption('count', false) ? $productCategory->FilterGroup->Filter : $productCategory->getFilterGroupForFilter();
    //$productFilterList = $productCategory->FilterGroup->Filter;

    foreach ($productFilterList as $productFilter)
    {
      // если фильтр типа "выбор" и всего одна опция, то игнор
      if (('choice' == $productFilter->type) && (count($productFilter->Property->Option) < 2)) continue;

      // если фильтр типа "диапазон" и макс. и мин. значения равны нулю, то игнор
      if (('range' == $productFilter->type) && !$productFilter->value_min && !$productFilter->value_max) continue;

      if (('range' == $productFilter->type) && ($productFilter->value_min == $productFilter->value_max)) continue;

      if (!$widget = call_user_func_array(array($this, 'getWidget'.sfInflector::camelize($productFilter->type)), array(
        $productFilter,
        'range' == $productFilter->type ? array('from' => $productFilter->value_min, 'to' => $productFilter->value_max) : array(),
      ))) continue;

      $index = "param-{$productFilter->id}";
      $this->setWidget($index, $widget);
      if ('range' == $productFilter->type)
      {
        $this->setDefault($index, array(
          'from' => $productFilter->value_min,
          'to'   => $productFilter->value_max,
        ));
      }

      $this->setValidator($index, new sfValidatorPass());
      $this->widgetSchema[$index]->setLabel($productFilter->name);
    }

    if ($productType)
    {
      $this->widgetSchema['type'] = new sfWidgetFormInputHidden();
      $this->validatorSchema['type'] = new sfValidatorPass();
      $this->setDefault('type', $productType->id);
    }

    $this->widgetSchema->setNameFormat('f[%s]');
  }

  public function buildQuery(myDoctrineQuery $q)
  {
    $productCategory = $this->getOption('productCategory');
    $productType = $this->getOption('productType', null);

    $filter = array(
      'category'   => $productCategory,
      'creator'    => isset($this->values['creator']) ? $this->values['creator'] : false,
      'price'      => (isset($this->values['price']['from']) && isset($this->values['price']['to'])) ? array(
        'from' => $this->values['price']['from'],
        'to'   => $this->values['price']['to'],
      ) : false,
      'parameters' => array(),
      'type'       => $productType,
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
        'formatter'       => array($this, 'show_part'),
      ),
    ));
  }

  protected function getWidgetRange($productFilter, array $value)
  {
    $id = uniqid();
    //myDebug::dump($productFilter);

    return new myWidgetFormRange(array(
      'value_from' => $value['from'],
      'value_to'   => $value['to'],
      'template'   => ''
        .'<div class="bSlide">'
          .'%value_from% %value_to%'
          .'<div class="sliderbox">'
            .'<div id="slider-'.$id.'" class="filter-range"></div>'
          .'</div>'
          .'<div class="pb5">'
            .'<input class="slider-from" type="hidden" disabled="disabled" value="'.$productFilter['value_min'].'" />'
            .'<input class="slider-to" type="hidden" disabled="disabled" value="'.$productFilter['value_max'].'" />'
            .'<span class="slider-interval"></span> '.(($productFilter instanceof ProductFilter) ? (!empty($productFilter->Property->unit) ? $productFilter->Property->unit : '') : '<span class="rubl">p</span>')
          .'</div>'
        .'</div>'

        .'<div class="clear"></div>'
    ), array(
      'class' => 'text',
      'style' => 'display: inline; width: 60px;',
    ));
  }

  protected function getWidgetCheckbox(ProductFilter $productFilter = null)
  {
    return new myWidgetFormInputCheckbox();
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
      $rows[] = $widget->renderContentTag('li', '<a href="#">еще...</a>', array('class' => 'bCtg__eMore', 'style' => 'padding-left: 10px;'));
    }

    return !$rows ? '' : $widget->renderContentTag('ul', implode($widget->getOption('separator'), $rows), array('class' => $widget->getOption('class')));
  }
}