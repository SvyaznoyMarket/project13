<?php

class myProductFormFilter extends sfFormFilter
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    if (!$productCategory = $this->getOption('productCategory'))
    {
      throw new InvalidArgumentException('You must provide a productCategory object.');
    }

    // виджет цены
    $this->widgetSchema['price'] = new myWidgetFormRange(array(
      'value_from' => 100,
      'value_to'   => 100000,
    ));
    $this->widgetSchema['price']->setLabel('Цена');
    $this->setDefault('price', array('from' => 500, 'to' => 3000));
    $this->validatorSchema['price'] = new sfValidatorPass();

    // виджет производителя
    $choices = CreatorTable::getInstance()
      ->getListByProductCategory($productCategory, array('select' => 'creator.id, creator.name'))
      ->toKeyValueArray('id', 'name')
    ;
    $this->widgetSchema['creator'] = new myWidgetFormChoice(array(
      'choices'  => $choices,
      'multiple' => true,
      'expanded' => true
    ));
    $this->widgetSchema['creator']->setLabel('Производитель');
    $this->validatorSchema['creator'] = new sfValidatorPass();

    // виджеты параметров
    $form = new BaseForm();
    foreach ($productCategory->FilterGroup->Filter as $productFilter)
    {
      if (!$widget = call_user_func(array($this, 'getWidget'.sfInflector::camelize($productFilter->type)), $productFilter)) continue;

      $form->setWidget($productFilter->id, $widget);
      $form->getWidgetSchema()->setLabel($productFilter->id, $productFilter->name);
      $form->setValidator($productFilter->id, new sfValidatorPass());
    }
    $this->embedForm('param', $form);
    $this->widgetSchema['param']->setLabel('Параметры');

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
    foreach ($this->values['param'] as $id => $param)
    {
      $productFilter = $productFilterList->getByIndex('id', $id);
      if (!$productFilter) continue;

      $filter['parameters'][] = array(
        'filter' => $productFilter,
        'values' => $param,
      );
    }

    ProductTable::getInstance()->setQueryForFilter($q, $filter);
  }

  protected function getWidgetChoice(ProductFilter $productFilter)
  {
    $choices = array();
    foreach ($productFilter->Property->Option as $productPropertyOption)
    {
      $choices[$productPropertyOption->id] = $productPropertyOption->value;
    }

    return new myWidgetFormChoice(array(
      'choices'  => $choices,
      'multiple' => $productFilter->is_multiple,
      'expanded' => true
    ));
  }

  protected function getWidgetRange(ProductFilter $productFilter)
  {
    return new myWidgetFormRange(array(
      'value_from' => 0,
      'value_to'   => 100,
    ));
  }
}