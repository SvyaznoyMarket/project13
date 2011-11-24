<?php

class UserDefaultsMock
{
    public function __get($name)
    {
        return '';
    }
}

class OrderStep1Form extends BaseOrderForm
{
  protected $_deliveryTypes = null;
//  protected $_deliveryIntervals = array();
  
  protected function isOrderContainBigProduct()
  {
      $bigThings = array(1096, 1095, 1094, 76, 18, 2);
      $furnitureCat = ProductCategoryTable::getInstance()->findOneByCoreId(80);
      foreach (sfContext::getInstance()->getUser()->getCart()->getProductServiceList() as $product)
      {
          foreach ($product['product']->Category as $category) {
              if ($category->root_id == $furnitureCat->root_id) {
                  return true;
              }
              if (in_array($category->core_id, $bigThings)) {
                  return true;
              }
              $ancs = $category->getNode()->getAncestors();
              foreach ($ancs as $anc) {
                  if (in_array($anc->core_id, $bigThings)) {
                      return true;
                  }
              }
          }
      }
      return false;
  }

  protected function isOrderHaveEnougthInStock($shop_id)
  {
      $cart = sfContext::getInstance()->getUser()->getCart()->getProducts();
      $stockRel = StockProductRelationTable::getInstance();
      foreach ($cart as $product_id => $product)
      {
          if (!$stockRel->isInStock($product_id, $shop_id, null, $product['cart']['quantity'])) {
              return false;
          }
      }
      return true;
  }

  protected function getDeliveryDateChoises($start = 0, $length = 7)
  {
    if ($start == 0 && date('H') >= 20) {
        $start = 1;
    }
    $stop = $start + $length;
    $choices = array();
    for ($i = $start; $i <= $stop; $i++)
    {
      $date = strtotime("+{$i} day");

      $prefix = '';
      $val = $prefix.date('d.m.Y', $date);
      if (0 == $i)
      {
        $prefix = 'сегодня ';
        $val = $prefix.'('.date('d.m.Y', $date).')';
      }
      if (1 == $i)
      {
        $prefix = 'завтра ';
        $val = $prefix.'('.date('d.m.Y', $date).')';
      }
      if (2 == $i)
      {
        $prefix = 'послезавтра ';
        $val = $prefix.'('.date('d.m.Y', $date).')';
      }

      $choices[date('Y-m-d', $date)] = $val;
    }
    return $choices;
  }
  
  protected function filterDeliveryTypes($deliveries)
  {
      $retval = array();
      foreach ($deliveries as $id => $deliveryType) {
          $retval[$id] = array(
              'label' => $deliveryType['label'],
              'description' => $deliveryType['description'],
          );
      }
      return $retval;
  }
  
  protected function filterDeliveryPeriods($periods)
  {
      $retval = array();
      foreach ($periods as $period) {
          $periodObj = DeliveryPeriodTable::getInstance()->findOneByCoreId($period['id']);
          if ($periodObj) {
            $retval[$periodObj->id] = $periodObj->name;
          }
      }
      return $retval;
  }

  public function getDeliveryTypes()
  {
    if ($this->_deliveryTypes === null) {
        $dProducts_raw = sfContext::getInstance()->getUser()->getCart()->getProducts();
        $dProducts = array();
        foreach ($dProducts_raw as $dProduct) {
            $dProducts[] = array('id' => $dProduct->core_id, 'quantity' => $dProduct->cart['quantity']);
        }
        $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
            'date' => date('Y-m-d'),
            'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
            'product' => $dProducts
        ));
        if (!$deliveries || !count($deliveries) || isset($deliveries['result'])) {
            $deliveries = array(array(
                'mode_id' => 1,
                'date' => date('Y-m-d', time()+(3600*48)),
                'price' => 0,
            ));
        }
        $deliveryTypes = array();

        sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
        foreach ($deliveries as $deliveryType) {
            $deliveryObj = DeliveryTypeTable::getInstance()->findOneByCoreId($deliveryType['mode_id']);
            $deliveryPeriod = round((strtotime($deliveryType['date']) - time()) / (3600 * 24));
            if ($deliveryPeriod < 0) $deliveryPeriod = 0;
            $deliveryTypes[$deliveryObj['id']] = array(
                'label' => $deliveryObj['name'],
                //'description' => $deliveryObj['description'],
                'description' => 'Доставка в течение '.format_number_choice('[0] дней|[1] 1 дня|{n: n % 10 > 1 && n % 10 < 5 && ( n < 11 || n > 14 ) && ( n % 100 < 11 || n % 100 > 14 ) } %1% дней|[5,+Inf] %1% дней ', array('%1%' => $deliveryPeriod), $deliveryPeriod). ', стоимостью '.$deliveryType['price'].' руб',
                'date_diff' => $deliveryPeriod,
                'periods' => empty($deliveryType['interval']) ? array() : $deliveryType['interval'],
            );
        }
        $this->_deliveryTypes = $deliveryTypes;
    }
    return $this->_deliveryTypes;
  }
    
  public function configure()
  {
    parent::configure();

    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $user = sfContext::getInstance()->getUser()->getGuardUser();
    if (!$user) {
        $user = new UserDefaultsMock;
    }

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
    $deliveryTypes = $this->getDeliveryTypes();
    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormChoice(array(
      'choices'         => $this->filterDeliveryTypes($deliveryTypes),
      'multiple'        => false,
      'expanded'        => true,
      'renderer_class'  => 'myWidgetFormOrderSelectRadio',
    ) );
//    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');
//    $this->validatorSchema['delivery_type_id'] = new sfValidatorChoice(array('choices' => array_keys($deliveryTypes), 'required' => false));
//    //$this->widgetSchema['receipt_type']->setOption('class', 'checkboxlist2');

    //$choices = DeliveryTypeTable::getInstance()->getChoices();
    /*if ('legal' == $this->object->person_type)
    {
      array_pop($choices);
      $this->object->delivery_type_id = DeliveryTypeTable::getInstance()->findOneByToken('standart')->id;
    }*/
    $defaultDelivery = DeliveryTypeTable::getInstance()->findOneByCoreId(1);
//    
//    if ($this->isOrderContainBigProduct()) {
//        $q = DeliveryTypeTable::getInstance()->createBaseQuery();
//        $q->addWhere('token != ?', 'self');
//        $this->widgetSchema['delivery_type_id'] = new sfWidgetFormDoctrineChoice(array(
//          //'choices'  => $choices,
//          'model'           => 'DeliveryType',
//          'method'          => 'getChoiceForOrder',
//          //'table_method'    => 'createBaseQuery',
//          'query'           => $q,
//          'default'         => $defaultDelivery->id,
//          'multiple'        => false,
//          'expanded'        => true,
//          'renderer_class'  => 'myWidgetFormOrderSelectRadio',
//        ));
//    } else {
//        $this->widgetSchema['delivery_type_id'] = new sfWidgetFormDoctrineChoice(array(
//          //'choices'  => $choices,
//          'model'           => 'DeliveryType',
//          'method'          => 'getChoiceForOrder',
//          'table_method'    => 'createBaseQuery',
//          'default'         => $defaultDelivery->id,
//          'multiple'        => false,
//          'expanded'        => true,
//          'renderer_class'  => 'myWidgetFormOrderSelectRadio',
//        ));
//    }
    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');
    $this->validatorSchema['delivery_type_id'] = new sfValidatorDoctrineChoice(array('model' => 'DeliveryType', 'required' => true));

    $choices = $this->getDeliveryDateChoises(max(0, $deliveryTypes[$defaultDelivery->id]['date_diff']));
    $this->widgetSchema['delivered_at'] = new sfWidgetFormChoice(array(
      'choices'  => $choices,
      'multiple' => false,
      'expanded' => false,
    ));
    $this->widgetSchema['delivered_at']->setLabel('Выберите дату доставки:');
    $this->validatorSchema['delivered_at'] = new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => true));

    $this->widgetSchema['delivery_period_id'] = new sfWidgetFormChoice(array(
      'choices'  => $this->filterDeliveryPeriods($deliveryTypes[$defaultDelivery->id]['periods']),
      'multiple' => false,
      'expanded' => false,
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
    $this->validatorSchema['recipient_last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

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
      $deliveryTypes = $this->getDeliveryTypes();
      $deliveryType = DeliveryTypeTable::getInstance()->find($taintedValues['delivery_type_id']);
      // если НЕ самовывоз
      if ($deliveryType && ('self' != $deliveryType->token))
      {
        $this->validatorSchema['delivery_type_id']->setOption('required', true);
        $this->validatorSchema['delivery_period_id']->setOption('required', true);
        $this->widgetSchema['delivery_period_id']->setOption('choices', $this->filterDeliveryPeriods($deliveryTypes[$taintedValues['delivery_type_id']]['periods']));
      }
      if ($deliveryType && ('self' == $deliveryType->token))
      {
      // если самовывоз
        if (!empty($taintedValues['shop_id'])) {
          $this->widgetSchema['delivered_at']->setOption('choices', $this->getDeliveryDateChoises(max(0, $deliveryTypes[$taintedValues['delivery_type_id']]['date_diff']),3));
//            if (!$this->isOrderHaveEnougthInStock($taintedValues['shop_id'])) {
//                $this->validatorSchema['delivered_at']->setOption('required', true);
//                $this->widgetSchema['delivered_at']->setOption('choices', $this->getDeliveryDateChoises(1,3));
//            } else {
//                $this->widgetSchema['delivered_at']->setOption('choices', $this->getDeliveryDateChoises(max(0, $deliveryTypes[$taintedValues['delivery_type_id']]['date_diff']),3));
//            }
        }
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