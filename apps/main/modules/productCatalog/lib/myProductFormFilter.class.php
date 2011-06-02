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

    // виджеты параметров
    $form = new BaseForm();
    foreach ($productCategory->FilterGroup->Filter as $productFilter)
    {
      if (!$widget = call_user_func(array($this, 'getWidget'.sfInflector::camelize($productFilter->type)), $productFilter)) continue;

      $form->setWidget($productFilter->id, $widget);
      $form->getWidgetSchema()->setLabel($productFilter->id, $productFilter->name);
    }
    $this->embedForm('parameter', $form);
    $this->widgetSchema['parameter']->setLabel('Параметры');

    $this->widgetSchema->setNameFormat('filter[%s]');
  }

  public function getFilter()
  {
    $filter = array();

    
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