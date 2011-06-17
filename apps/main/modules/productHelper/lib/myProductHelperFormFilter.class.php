<?php

class myProductHelperFormFilter extends sfFormFilter
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    if (!$productHelper = $this->getOption('productHelper'))
    {
      throw new InvalidArgumentException('You must provide a productHelper object.');
    }

    foreach ($productHelper->Question as $productHelperQuestion)
    {
      $this->widgetSchema[$productHelperQuestion->id] = new sfWidgetFormChoice(array(
        'choices'  => $productHelperQuestion->Answer->toKeyValueArray('id', 'name'),
        'multiple' => false,
        'expanded' => true,
      ));
      $this->validatorSchema[$productHelperQuestion->id] = new sfValidatorPass();

      $this->widgetSchema[$productHelperQuestion->id]->setLabel((string)$productHelperQuestion);
    }

    $this->widgetSchema->setNameFormat('answer[%s]');
  }

  public function buildQuery(myDoctrineQuery $q)
  {
    $productHelper = $this->getOption('productHelper');

    $filter = array(
      'parameters' => array(),
    );

    $productHelperFilterList = ProductHelperFilterTable::getInstance()->getListByAnswerIds(array_values($this->getValues()));
    foreach ($productHelperFilterList as $productHelperFilter)
    {
      $filter['parameters'][] = array(
        'filter' => $productHelperFilter->ProductFilter,
        'values' => sfYaml::load($productHelperFilter->value),
      );
    }

    ProductTable::getInstance()->setQueryForFilter($q, $filter);
  }
}