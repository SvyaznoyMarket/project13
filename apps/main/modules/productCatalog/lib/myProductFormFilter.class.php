<?php

class myProductFormFilter extends sfFormFilter
{
  public function configure()
  {
    $this->disableCSRFProtection();

    if (!$productCategory = $this->getOption('productCategory'))
    {
      throw new InvalidArgumentException('You must provide a productCategory object.');
    }

    $this->widgetSchema['price'] = new myWidgetFormRange(array(
      'value_from' => 100,
      'value_to'   => 100000,
    ));
    $this->widgetSchema['price']->setLabel('Цена');
    $this->setDefault('price', array('from' => 500, 'to' => 3000));

    $form = new sfForm();
    foreach ($productCategory->FilterGroup->Filter as $productFilter)
    {
      if (!$widget = call_user_func(array($this, 'get'.sfInflector::camelize($productFilter->type).'Widget'), $productFilter)) continue;

      $form->setWidget($productFilter->id, $widget);
      $form->getWidgetSchema()->setLabel($productFilter->id, $productFilter->name);
    }
    $this->embedForm('parameter', $form);
    $this->widgetSchema['parameter']->setLabel('Параметры');
  }

  protected function getChoiceWidget(ProductFilter $productFilter)
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

  protected function getRangeWidget(ProductFilter $productFilter)
  {
    return new myWidgetFormRange(array(
      'value_from' => 0,
      'value_to'   => 100,
    ));
  }
}