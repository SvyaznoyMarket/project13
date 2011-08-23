<?php

class OrderStep1Form extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['region_id'] =
      !empty($this->object->region_id)
      ? new sfWidgetFormInputHidden()
      : new sfWidgetFormDoctrineChoice(array('model' => 'Region', 'add_empty' => true))
    ;
    $this->widgetSchema['region_id']->setLabel('Город');
    $this->validatorSchema['region_id'] = new sfValidatorDoctrineChoice(array('model' => 'Region', 'required' => true));

    $this->widgetSchema['person_type'] = new sfWidgetFormChoice(array(
      'choices'  => array('individual' => 'для себя (как частное лицо)', 'legal' => 'для компании (на юридическое лицо)'),
      'multiple' => false,
      'expanded' => true,
    ));
    $this->widgetSchema['person_type']->setLabel('Вы покупаете');
    $this->validatorSchema['person_type'] = new sfValidatorChoice(array('choices' => OrderTable::getInstance()->getEnumValues('person_type'), 'required' => false));

    $this->widgetSchema['receipt_type'] = new sfWidgetFormChoice(array(
      'choices'  => array('pickup' => 'самовывоз', 'delivery' => 'доставка'),
      'multiple' => false,
      'expanded' => true,
    ), array('class' => 'inline'));
    $this->widgetSchema['receipt_type']->setLabel('Способ получения');
    $this->validatorSchema['receipt_type'] = new sfValidatorChoice(array('choices' => OrderTable::getInstance()->getEnumValues('receipt_type'), 'required' => false));

    $choices = DeliveryTypeTable::getInstance()->getChoices();
    if ('legal' == $this->object->person_type)
    {
      array_pop($choices);
      $this->object->delivery_type_id = DeliveryTypeTable::getInstance()->findOneByToken('standart')->id;
    }
    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormChoice(array(
      'choices'  => $choices,
      'multiple' => false,
      'expanded' => true,
    ));
    $this->widgetSchema['delivery_type_id']->setLabel('Тип доставки');
    $this->validatorSchema['delivery_type_id'] = new sfValidatorDoctrineChoice(array('model' => 'DeliveryType', 'required' => false));

    $choices = array('' => '');
    for ($i = 1; $i <= 7; $i++)
    {
      $date = strtotime("+{$i} day");

      $prefix = '';
      if (1 == $i)
      {
        $prefix = 'завтра ';
      }
      if (2 == $i)
      {
        $prefix = 'послезавтра ';
      }

      $choices[date('Y-m-d', $date)] = $prefix.date('d.m.Y', $date);
    }
    $this->widgetSchema['delivered_at'] = new sfWidgetFormChoice(array(
      'choices'  => $choices,
      'multiple' => false,
      'expanded' => false,
    ));
    $this->widgetSchema['delivered_at']->setLabel('Дата доставки');
    $this->validatorSchema['delivered_at'] = new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false));

    $this->widgetSchema['address'] = new sfWidgetFormInputText();
    $this->widgetSchema['address']->setLabel('Адрес');
    $this->validatorSchema['address'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['shop_id'] = new sfWidgetFormChoice(array(
      'choices'  => myToolkit::arrayDeepMerge(array('' => ''), ShopTable::getInstance()->getListByRegion($this->object->region_id)->toKeyValueArray('id', 'name')),
      'multiple' => false,
      'expanded' => false,
    ));
    $this->widgetSchema['shop_id']->setLabel('Магазин');
    $this->validatorSchema['shop_id'] = new sfValidatorDoctrineChoice(array('model' => 'Shop', 'required' => false));

    $this->useFields(array(
      'region_id',
      'person_type',
      'receipt_type',
      'shop_id',
      'delivery_type_id',
      'delivered_at',
      'address',
    ));

    //$this->validatorSchema->setOption('allow_extra_fields', true);

    $this->widgetSchema->setNameFormat('order[%s]');
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $fields = array(
      'region_id',
    );
    // если указан регион
    if (!empty($this->object->region_id))
    {
      foreach (array(
        'person_type',
        'receipt_type',
      ) as $name) {
        $this->validatorSchema[$name]->setOption('required', true);
      }

      if (!empty($taintedValues['receipt_type']))
      {
        if ('delivery' == $taintedValues['receipt_type'])
        {
          foreach (array(
            'delivery_type_id',
            'delivered_at',
            'address',
          ) as $name) {
            $this->validatorSchema[$name]->setOption('required', true);
          }
        }
        else if ('pickup' == $taintedValues['receipt_type'])
        {
          $this->validatorSchema['shop_id']->setOption('required', true);
        }
      }

      if ('legal' == $this->object->person_type)
      {
        $this->validatorSchema['delivery_type_id']->setOption('required', false);
      }
    }

    parent::bind($taintedValues, $taintedFiles);
  }
}