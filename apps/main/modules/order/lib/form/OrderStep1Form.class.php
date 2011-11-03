<?php

class OrderStep1Form extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $user = sfContext::getInstance()->getUser()->getGuardUser();

    $this->disableCSRFProtection();

    $this->widgetSchema['region_id'] = new sfWidgetFormChoice(array(
      'choices'  => RegionTable::getInstance()->findByType('city')->toKeyValueArray('id', 'name'),
      'multiple' => false,
      'expanded' => false,
      'renderer_class'  => 'myWidgetFormOrderSelect',
    )/*, array(
      'data-url' => url_for('region_autocomplete', array('type' => 'city')),
	  'renderer_class'  => 'myWidgetFormOrderSelect',
    )*/);
    $this->widgetSchema['region_id']->setLabel('В каком городе вы будете получать заказ?'); //->setLabel('Город');
    $this->validatorSchema['region_id'] = new sfValidatorDoctrineChoice(array('model' => 'Region', 'required' => true));

    $this->widgetSchema['person_type'] = new sfWidgetFormChoice(array(
      'choices'  => array('individual' => 'для себя (как частное лицо)', 'legal' => 'для компании (на юридическое лицо)'),
      'multiple' => false,
      'expanded' => true,
    ));
    $this->widgetSchema['person_type']->setLabel('Вы покупаете');
    $this->validatorSchema['person_type'] = new sfValidatorChoice(array('choices' => OrderTable::getInstance()->getEnumValues('person_type'), 'required' => false));

    // !!!!!!!!!!
    $dProducts_raw = sfContext::getInstance()->getUser()->getCart()->getProducts();
    $dProducts = array();
    foreach ($dProducts_raw as $dProduct) {
        $dProducts[] = array('id' => $dProduct->id, 'quantity' => $dProduct->cart['quantity']);
    }
    $delivery = Core::getInstance()->query('delivery.calc', array(), array(
        'date' => date('Y-m-d'),
        'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
        'product' => $dProducts
    ));
    $deliveryTypes = array();
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    foreach ($delivery as $deliveryType) {
        $deliveryTypes[$deliveryType['delivery_id']] = array(
            'label' => $deliveryType['name'],
            //'description' => 'Доставка в течение '.$deliveryType['period']. ' дней, стоимостью '.$deliveryType['price'].' руб',
            'description' => 'Доставка в течение '.format_number_choice('[0] дней|[1] 1 дня|{n: n % 10 > 1 && n % 10 < 5 && ( n < 11 || n > 14 ) && ( n % 100 < 11 || n % 100 > 14 ) } %1% дней|[5,+Inf] %1% дней ', array('%1%' => $deliveryType['period']), $deliveryType['period']). ', стоимостью '.$deliveryType['price'].' руб'
        );
    }
    
    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormChoice(array(
      'choices'         => $deliveryTypes,
      'multiple'        => false,
      'expanded'        => true,
      'renderer_class'  => 'myWidgetFormOrderSelectRadio',
    ) );
//    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ доставки:');
//    $this->validatorSchema['delivery_type_id'] = new sfValidatorChoice(array('choices' => OrderTable::getInstance()->getEnumValues('receipt_type'), 'required' => false));
//    //$this->widgetSchema['receipt_type']->setOption('class', 'checkboxlist2');

    //$choices = DeliveryTypeTable::getInstance()->getChoices();
    /*if ('legal' == $this->object->person_type)
    {
      array_pop($choices);
      $this->object->delivery_type_id = DeliveryTypeTable::getInstance()->findOneByToken('standart')->id;
    }*/
//    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormDoctrineChoice(array(
//      //'choices'  => $choices,
//      'model'           => 'DeliveryType',
//      'method'          => 'getChoiceForOrder',
//      'table_method'    => 'createBaseQuery',
//      'multiple'        => false,
//      'expanded'        => true,
//      'renderer_class'  => 'myWidgetFormOrderSelectRadio',
//    ));
    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');
    //$this->validatorSchema['delivery_type_id'] = new sfValidatorDoctrineChoice(array('model' => 'DeliveryType', 'required' => true));

    $choices = array();
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
    $this->widgetSchema['delivered_at']->setLabel('Выберите дату доставки:');
    $this->validatorSchema['delivered_at'] = new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false));

    $this->widgetSchema['delivery_period_id'] = new sfWidgetFormDoctrineChoice(array(
      'model'           => 'DeliveryPeriod',
      'add_empty'       => false,
      'expanded'        => false,
      'renderer_class'  => 'myWidgetFormOrderSelect',
      'query'           => $this->object->delivery_type_id ? DeliveryPeriodTable::getInstance()->createBaseQuery()->addWhere('deliveryPeriod.delivery_type_id = ?', $this->object->delivery_type_id) : null,
    ));
    $this->validatorSchema['delivery_period_id'] = new sfValidatorDoctrineChoice(array('model' => 'DeliveryPeriod', 'required' => false));

    $this->widgetSchema['address'] = new sfWidgetFormInputText();
	  $this->widgetSchema['address']->setDefault($user->address);
    $this->widgetSchema['address']->setLabel('Адрес доставки:');
    $this->validatorSchema['address'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['shop_id'] = new sfWidgetFormChoice(array(
//      'choices'  => myToolkit::arrayDeepMerge(array('' => ''), ShopTable::getInstance()->getListByRegion($this->object->region_id)->toKeyValueArray('id', 'name')),
//      'choices'  => ShopTable::getInstance()->getListByRegion($this->object->region_id)->toKeyValueArray('id', 'name'),
      'choices'         => ShopTable::getInstance()->getChoices(),
      'multiple'        => false,
      'expanded'        => false,
      'renderer_class'  => 'myWidgetFormOrderSelect',
    ));
    $this->widgetSchema['shop_id']->setLabel('Выберите магазин, в котором хотите получить заказ:');
    $this->validatorSchema['shop_id'] = new sfValidatorDoctrineChoice(array('model' => 'Shop', 'required' => false));

    //$this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema['payment_method_id'] = new sfWidgetFormDoctrineChoice(array(
      'model'           => 'PaymentMethod',
      'method'          => 'getChoiseForOrder',
      'add_empty'       => false,
      'expanded'        => true,
      'renderer_class'  => 'myWidgetFormOrderSelectRadio',
    ));
    $this->widgetSchema['payment_method_id']->setLabel('Выберите способ оплаты:');
    $this->validatorSchema['payment_method_id'] = new sfValidatorDoctrineChoice(array('model' => 'PaymentMethod', 'required' => true));

    $this->widgetSchema['recipient_last_name'] = new sfWidgetFormInputText();
	$this->widgetSchema['recipient_last_name']->setDefault($user->last_name);
    $this->widgetSchema['recipient_last_name']->setLabel('Фамилия получателя:');
    $this->validatorSchema['recipient_last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
	$this->widgetSchema['recipient_first_name']->setDefault($user->first_name);
    $this->widgetSchema['recipient_first_name']->setLabel('Имя получателя:');
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['recipient_phonenumbers'] = new sfWidgetFormInputText();
	$this->widgetSchema['recipient_phonenumbers']->setDefault($user->phonenumber);
    $this->widgetSchema['recipient_phonenumbers']->setLabel('Мобильный телефон для связи:');
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('max_length' => 255, ));

    //$choices = array(1 => 'Я хочу получать СМС уведомления об изменении статуса заказа');
    $this->widgetSchema['is_receive_sms'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema['is_receive_sms']->setLabel('Я хочу получать СМС уведомления об изменении статуса заказа');
    $this->validatorSchema['is_receive_sms'] = new sfValidatorBoolean();

//    $this->widgetSchema['zip_code'] = new sfWidgetFormInputText();
//    $this->widgetSchema['zip_code']->setLabel('Почтовый индекс:');
//    $this->validatorSchema['zip_code'] = new sfValidatorPass();

    $this->widgetSchema['extra'] = new sfWidgetFormTextarea();
	$this->widgetSchema['extra']->setAttribute('cols', null)->setAttribute('style', 'width:100%;');
    $this->widgetSchema['extra']->setLabel('Комментарии:');
    $this->validatorSchema['extra'] = new sfValidatorPass();

    /*$this->widgetSchema['recipient_middle_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_middle_name']->setLabel('Отчество');
    $this->validatorSchema['recipient_middle_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));*/

    $this->useFields(array(
      'region_id',
      'person_type',
      //'receipt_type',
      'delivery_type_id',
      'shop_id',
      'delivered_at',
      'delivery_period_id',
      'recipient_first_name',
      'recipient_last_name',
      'recipient_phonenumbers',
      'is_receive_sms',
      //'zip_code',
      'address',
      'extra',
      //'recipient_middle_name',
      'payment_method_id',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    // если указан регион
    /*if (!empty($this->object->region_id))
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
    }*/

    // проверяет типа доставки
    if (!empty($taintedValues['delivery_type_id']))
    {
      $deliveryType = DeliveryTypeTable::getInstance()->find($taintedValues['delivery_type_id']);
      // если НЕ самовывоз
      if ($deliveryType && ('self' != $deliveryType->token))
      {
        $this->validatorSchema['delivery_type_id']->setOption('required', true);
        $this->validatorSchema['delivery_period_id']->setOption('required', true);
      }
    }

    parent::bind($taintedValues, $taintedFiles);
  }

  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);

    if (!empty($values['delivery_type_id']))
    {
      $deliveryType = DeliveryTypeTable::getInstance()->find($values['delivery_type_id']);
      // если самовывоз
      if ($deliveryType && ('self' == $deliveryType->token))
      {
        $this->object->address = $this->object->Shop->address;
      }
      else
      {
        $this->object->shop_id = null;
      }
    }
  }
}