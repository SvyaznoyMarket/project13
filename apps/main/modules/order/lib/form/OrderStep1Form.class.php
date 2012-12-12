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

  protected function isOrderHaveEnougthInStock($shop_id)
  {
    $cart = sfContext::getInstance()->getUser()->getCart()->getProducts();
    $stockRel = StockProductRelationTable::getInstance();
    foreach ($cart as $product_id => $product)
    {
      /** @var $product \light\ProductCartData */

      $product_id = ProductTable::getInstance()->getIdBy('core_id',$product_id);
      if (!$stockRel->isInStock($product_id, $shop_id, null, $product->getQuantity())) {
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

      if (in_array(date('dmY', $date), array('30122011', '31122011', '01012012', '02012012', '03012012'))) {
        $stop++;
        continue;
      }
      $prefix = '';
      $val = $prefix . date('d.m.Y', $date);
      if (0 == $i) {
        $prefix = 'сегодня ';
        $val = $prefix . '(' . date('d.m.Y', $date) . ')';
      }
      if (1 == $i) {
        $prefix = 'завтра ';
        $val = $prefix . '(' . date('d.m.Y', $date) . ')';
      }
      if (2 == $i) {
        $prefix = 'послезавтра ';
        $val = $prefix . '(' . date('d.m.Y', $date) . ')';
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
      $retval[$period['id']] = 'с ' . $period['time_begin'] . ' до ' . $period['time_end'];
    }
    return $retval;
  }

  public function getDeliveryTypes()
  {
    if ($this->_deliveryTypes === null) {
      $formatPrice = function($price)
      {
        if ($price === null) {
          return '';
        }
        if ($price > 0) {
          return ', ' . $price . ' руб.';
        } else {
          return ', бесплатно.';
        }
      };
      $dProducts_raw = sfContext::getInstance()->getUser()->getCart()->getProducts();
      $dProducts = array();
      foreach ($dProducts_raw as $dProductId => $dProduct) {
        /** @var $dProduct \light\ProductCartData */
        $dProducts[] = array('id' => $dProductId, 'quantity' => $dProduct->getQuantity());
      }
      $deliveries = Core::getInstance()->query('delivery.calc', array(), array(
        'geo_id' => sfContext::getInstance()->getUser()->getRegion('core_id'),
        'product' => $dProducts
      ));
      if (!$deliveries || !count($deliveries) || isset($deliveries['result'])) {
        $deliveries = array(array(
          'mode_id' => 1,
          'date' => date('Y-m-d', time() + (3600 * 48)),
          'price' => null,
        ));
      }
      $deliveryTypes = array();

      foreach ($deliveries as $deliveryType) {
        $modeId = $deliveryType['mode_id'];
        $deliveryObj = DeliveryTypeTable::getInstance()->findOneByCoreId($modeId);
        $minDeliveryDate = DateTime::createFromFormat('Y-m-d', $deliveryType['date']);
        $now = new DateTime();
        $deliveryPeriod = $minDeliveryDate->diff($now)->days;
        if ($deliveryPeriod < 0) $deliveryPeriod = 0;
        $deliveryPeriod = myToolkit::fixDeliveryPeriod($modeId, $deliveryPeriod);
        if ($deliveryPeriod === false) continue;
        if ($deliveryType['mode_id'] == 5) {
          $label = $deliveryObj['name'];
        } else {
          $label = $deliveryObj['name'] . $formatPrice($deliveryType['price']);
        }
        $deliveryTypes[$deliveryObj['id']] = array(
          'label' => $label,
          'description' => $deliveryObj['description'],
          //'description' => 'Доставка '.myToolkit::formatDeliveryDate($deliveryPeriod). ', стоимостью '.$deliveryType['price'].' руб',
          'date_diff' => $deliveryPeriod,
          'periods' => empty($deliveryType['interval']) ? array() : $deliveryType['interval'],
          'price' => $deliveryType['price'],
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

    $regions = RepositoryManager::getRegion()->getShowInMenu();
    $region_choises = array();
    foreach ($regions as $region)
    {
      $region_choices[$region->getId()]['name'] = $region->getName();
      $region_choices[$region->getId()]['data-url'] = url_for('region_change', $region->getId());
    }

    $this->widgetSchema['region_id'] = new sfWidgetFormChoice(array(
        'choices' => $region_choices,
        'multiple' => false,
        'expanded' => false,
        'renderer_class' => 'myWidgetFormOrderSelect',
      )
    /*, array(
      'data-url' => url_for('region_autocomplete', array('type' => 'city')),
    'renderer_class'  => 'myWidgetFormOrderSelect',
    )*/);
    $this->widgetSchema['region_id']->setLabel('В каком городе вы будете получать заказ?'); //->setLabel('Город');
    $this->validatorSchema['region_id'] = new sfValidatorDoctrineChoice(array('model' => 'Region', 'required' => true));

    $this->widgetSchema['person_type'] = new sfWidgetFormChoice(array(
      'choices' => array('individual' => 'для себя (как частное лицо)', 'legal' => 'для компании (на юридическое лицо)'),
      'multiple' => false,
      'expanded' => true,
    ));
    $this->widgetSchema['person_type']->setLabel('Вы покупаете');
    $this->validatorSchema['person_type'] = new sfValidatorChoice(array('choices' => OrderTable::getInstance()->getEnumValues('person_type'), 'required' => false));

    // !!!!!!!!!!
    $deliveryTypes = $this->getDeliveryTypes();
    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormChoice(array(
      'choices' => $this->filterDeliveryTypes($deliveryTypes),
      'multiple' => false,
      'expanded' => true,
      'renderer_class' => 'myWidgetFormOrderSelectRadio',
    ));
    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');
    $this->validatorSchema['delivery_type_id'] = new sfValidatorChoice(array('choices' => array_keys($deliveryTypes), 'required' => false));
    //$this->widgetSchema['receipt_type']->setOption('class', 'checkboxlist2');

    //    $choices = DeliveryTypeTable::getInstance()->getChoices();
    //    if ('legal' == $this->object->person_type)
    //    {
    //      array_pop($choices);
    //      $this->object->delivery_type_id = DeliveryTypeTable::getInstance()->findOneByToken('standart')->id;
    //    }
    $defaultDelivery = DeliveryTypeTable::getInstance()->findOneByCoreId(1);

    if (isset($deliveryTypes[$defaultDelivery->id])) {
      $choices = $this->getDeliveryDateChoises(max(0, $deliveryTypes[$defaultDelivery->id]['date_diff']));
    } else {
      $choices = array();
    }
    $this->widgetSchema['delivered_at'] = new sfWidgetFormChoice(array(
      'choices' => $choices,
      'multiple' => false,
      'expanded' => false,
    ));
    $this->widgetSchema['delivered_at']->setLabel('Выберите дату доставки:');
    $this->validatorSchema['delivered_at'] = new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => true));

    $this->widgetSchema['delivery_period_id'] = new sfWidgetFormChoice(array(
      'choices' => array(), // $defaultDelivery->DeliveryPeriod,//$this->filterDeliveryPeriods($deliveryTypes[$defaultDelivery->id]['periods']),
      'multiple' => false,
      'expanded' => false,
    ));
    //$this->validatorSchema['delivery_period_id'] = new sfValidatorDoctrineChoice(array('model' => 'DeliveryPeriod', 'required' => false));

    $this->widgetSchema['address'] = new sfWidgetFormInputText();
    $this->widgetSchema['address']->setDefault($user->address);
    $this->widgetSchema['address']->setLabel('Адрес доставки:');
    $this->validatorSchema['address'] = new sfValidatorString(array('required' => false));

    $this->widgetSchema['shop_id'] = new sfWidgetFormChoice(array(
//      'choices'  => myToolkit::arrayDeepMerge(array('' => ''), ShopTable::getInstance()->getListByRegion($this->object->region_id)->toKeyValueArray('id', 'name')),
//      'choices'  => ShopTable::getInstance()->getListByRegion($this->object->region_id)->toKeyValueArray('id', 'name'),
      'choices' => DeliveryCalc::getShopListForSelfDelivery(),
      'multiple' => false,
      'expanded' => false,
      'renderer_class' => 'myWidgetFormOrderSelect',
    ));
    $this->widgetSchema['shop_id']->setLabel('Выберите магазин, в котором хотите получить заказ:');
    $this->validatorSchema['shop_id'] = new sfValidatorDoctrineChoice(array('model' => 'Shop', 'required' => false));

    //$this->validatorSchema->setOption('allow_extra_fields', true);
    $this->widgetSchema['payment_method_id'] = new sfWidgetFormDoctrineChoice(array(
      'model' => 'PaymentMethod',
      'method' => 'getChoiseForOrder',
      'add_empty' => false,
      'expanded' => true,
      'renderer_class' => 'myWidgetFormOrderSelectRadio',
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
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('max_length' => 255,));

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

    $this->widgetSchema['agreed'] = new sfWidgetFormInputCheckbox();
    $this->widgetSchema['agreed']->setLabel('Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»');
    $this->validatorSchema['agreed'] = new sfValidatorBoolean(array('required' => true), array('required' => 'Пожалуйста, ознакомьтесь с условиями продажи и правовой информацией и поставьте галочку'));

	  $this->widgetSchema['sclub_card_number'] = new sfWidgetFormInputText();
	  $this->widgetSchema['sclub_card_number']->setLabel('Номер карты для зачисления баллов');
	  $this->validatorSchema['sclub_card_number'] = new myValidatorSClubCardNumber(array('required' => false), array('invalid' => 'номер карточки введен неверно'));

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
	    'sclub_card_number',
      //'recipient_middle_name',
      'payment_method_id',
      'agreed'
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

    // myDebug::dump($deliveryTypes);
    // myDebug::dump($taintedValues);
    // проверяет типа доставки
    if (!empty($taintedValues['delivery_type_id'])) {

      $deliveryTypes = $this->getDeliveryTypes();
      $deliveryType = DeliveryTypeTable::getInstance()->find($taintedValues['delivery_type_id']);
      // если НЕ самовывоз
      if ($deliveryType && ('self' != $deliveryType->token)) {
        $choices = $this->getDeliveryDateChoises(max(0, $deliveryTypes[$taintedValues['delivery_type_id']]['date_diff']));
        $periods = $this->filterDeliveryPeriods($deliveryTypes[$taintedValues['delivery_type_id']]['periods']);
        $this->widgetSchema['delivered_at']->setOption('choices', $choices);
        $this->validatorSchema['delivered_at']->setOption('choices', array_keys($choices));
        $this->validatorSchema['delivery_type_id']->setOption('required', true);
        if (count($periods) > 0) {
          $this->validatorSchema['delivery_period_id']->setOption('required', true);
        } else {
          $this->validatorSchema['delivery_period_id']->setOption('required', false);
          $this->widgetSchema['delivery_period_id']->setOption('is_hidden', true);
        }
        $this->widgetSchema['delivery_period_id']->setOption('choices', $periods);
      }
      if ($deliveryType && ('self' == $deliveryType->token)) {
        // если самовывоз
        if (!empty($taintedValues['shop_id'])) {
          // чтобы не срабатывал валидатор, так как при самовывозе этого поля в форме нет.
          unset($taintedValues['delivery_period_id']);

          //          $choices = $this->getDeliveryDateChoises(DeliveryCalc::getMinDateForShopSelfDelivery($taintedValues['shop_id'], true), 3);
          $choices = $this->getDeliveryDateChoises(max(0, DeliveryCalc::getMinDateForShopSelfDelivery($taintedValues['shop_id'], true), $deliveryTypes[$taintedValues['delivery_type_id']]['date_diff']), 3);
          $this->widgetSchema['delivered_at']->setOption('choices', $choices);
          $this->validatorSchema['delivered_at']->setOption('choices', array_keys($choices));
          $this->validatorSchema['delivery_period_id']->setOption('required', false);
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

    if (!empty($values['delivery_type_id'])) {
      $deliveryType = DeliveryTypeTable::getInstance()->find($values['delivery_type_id']);
      // если самовывоз
      if ($deliveryType && ('self' == $deliveryType->token)) {
        $this->object->address = $this->object->Shop->address;
      }
      else
      {
        $this->object->shop_id = null;
      }
    }
  }
}