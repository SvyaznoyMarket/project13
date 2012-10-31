<?php

class OrderDefaultForm extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    if (!$user = $this->getOption('user', sfContext::getInstance()->getUser()))
    {
      throw new Exception('User not provided');
    }

    if (!$deliveryTypes = $this->getOption('deliveryTypes', false))
    {
      throw new Exception('Delivery types not provided');
    }

    $region = $user->getRegion();

    // TODO: использовать новую сущность
    $hasMetro = 14974 == $region['core_id'];

    // region_id
    $this->widgetSchema['region_id'] = new myWidgetFormComponent(array('component' => array('order_', 'field_region_id'), 'component_param' => array()));
    $this->widgetSchema['region_id']->setLabel('В каком городе вы будете получать заказ?');

    // delivery_type_id
    $this->widgetSchema['delivery_type_id'] = new myWidgetFormComponent(array(
      'component' => array('order_', 'field_delivery_type_id'),
      'component_param' => array('deliveryTypes' => $deliveryTypes),
    ));
    $this->validatorSchema['delivery_type_id'] = new sfValidatorChoice(array('choices' => array_map(function($i) { return $i->getId(); }, $deliveryTypes), 'required' => true), array('required' => 'Выберите способ получения заказа'));
    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');
    if (1 == count($deliveryTypes)) {
      $this->widgetSchema['delivery_type_id']->setDefault($deliveryTypes[0]->getId());
    }

    // recipient_first_name
    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Укажите имя получателя'));
    $this->widgetSchema['recipient_first_name']->setLabel('Имя получателя:');

    // recipient_first_name
    $this->widgetSchema['recipient_last_name'] = new sfWidgetFormInputText();
    $this->validatorSchema['recipient_last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Укажите фамилию получателя'));
    $this->widgetSchema['recipient_last_name']->setLabel('Фамилия получателя:');

    // recipient_phonenumbers
    $this->widgetSchema['recipient_phonenumbers'] = new sfWidgetFormInputText();
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('min_length' => 7, 'max_length' => 20, 'required' => true, 'trim' => true), array('required' => 'Укажите телефон для связи', 'min_length' => 'Неправильный телефонный номер', 'max_length' => 'Неправильный телефонный номер'));
    $this->widgetSchema['recipient_phonenumbers']->setLabel('Мобильный телефон для связи:');

    // is_receive_sms
    $this->widgetSchema['is_receive_sms'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_receive_sms'] = new sfValidatorBoolean();
    $this->widgetSchema['is_receive_sms']->setLabel('Я хочу получать СМС уведомления об изменении статуса заказа');

    // address
    /*
    $this->widgetSchema['address'] = new sfWidgetFormInputText();
    $this->validatorSchema['address'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите адрес доставки'));
    $this->widgetSchema['address']->setLabel('Адрес доставки:');
    */

    $this->widgetSchema['address_metro'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_metro'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите метро'));
    $this->widgetSchema['address_metro']->setLabel('Метро');

    $this->widgetSchema['address_street'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_street'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите улицу'));
    $this->widgetSchema['address_street']->setLabel('Улица');

    $this->widgetSchema['address_building'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_building'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите дом'));
    $this->widgetSchema['address_building']->setLabel('Дом');

    $this->widgetSchema['address_number'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_number'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите корпус'));
    $this->widgetSchema['address_number']->setLabel('Корпус');

    $this->widgetSchema['address_apartment'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_apartment'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите номер квартиры'));
    $this->widgetSchema['address_apartment']->setLabel('Квартира');

    $this->widgetSchema['address_floor'] = new sfWidgetFormInputText();
    $this->validatorSchema['address_floor'] = new sfValidatorString(array('required' => false), array('required' => 'Укажите этаж'));
    $this->widgetSchema['address_floor']->setLabel('Этаж');

    // extra
    $this->widgetSchema['extra'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extra'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['extra']->setLabel('Комментарии:');

    $this->widgetSchema['credit_bank_id'] = new sfWidgetFormInput();
    $this->validatorSchema['credit_bank_id'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['credit_bank_id']->setLabel('');

    // sclub_card_number
    $this->widgetSchema['sclub_card_number'] = new sfWidgetFormInputText();
    $this->widgetSchema['sclub_card_number']->setLabel('Номер карточки связного клуба');
    $this->validatorSchema['sclub_card_number'] = new myValidatorSClubCardNumber(array('required' => false), array('invalid' => 'В номере карты допущена ошибка. Проверьте правильность ввода номера и повторите попытку'));

    // certificate
    $this->widgetSchema['cardnumber'] = new sfWidgetFormInputText();
    $this->validatorSchema['cardnumber'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['cardpin'] = new sfWidgetFormInputText();
    $this->validatorSchema['cardpin'] = new sfValidatorString(array('required' => false));

    // payment_method_id
    $choices = array();
    $this->widgetSchema['payment_method_id'] = new myWidgetFormComponent(array(
      'component' => array('order_', 'field_payment_method_id'),
      'component_param' => array(),
    ));
    $this->validatorSchema['payment_method_id'] = new sfValidatorInteger(array('required' => true), array('required' => 'Укажите cпособ оплаты'));
    $this->widgetSchema['payment_method_id']->setLabel('Выберите способ оплаты:');

    // agreed
    $this->widgetSchema['agreed'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['agreed'] = new sfValidatorBoolean(array('required' => true), array('required' => 'Пожалуйста, ознакомьтесь с условиями продажи и правовой информацией и поставьте галочку'));
    $this->widgetSchema['agreed']->setLabel('Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»');

    // использовать поля
    $fields = array(
      'region_id',
      'delivery_type_id',
      'recipient_first_name',
      'recipient_last_name',
      'recipient_phonenumbers',
      'is_receive_sms',
      //'address',
      'address_street',
      'address_number',
      'address_building',
      'address_apartment',
      'address_floor',
      'extra',
      'credit_bank_id',
      'sclub_card_number',
      'payment_method_id',
      'agreed',
      'cardnumber',
      'cardpin',
    );
    if ($hasMetro)
    {
      $fields[] = 'address_metro';
    }
    $this->useFields($fields);

    $this->widgetSchema->setNameFormat('order[%s]');
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    if (!empty($taintedValues['delivery_type_id'])) {
      if ($deliveryType = DeliveryTypeTable::getInstance()->find($taintedValues['delivery_type_id'])) {
        if ('standart' == $deliveryType->token) {
          $this->validatorSchema['address_street']->setOption('required', true);
          $this->validatorSchema['address_number']->setOption('required', true);
        }
      }
    }

    // если оплата подарочной картой
    if (!empty($taintedValues['payment_method_id']) && (9 == $taintedValues['payment_method_id'])) {
        $this->validatorSchema['cardnumber'] = new myValidatorCertificate(array('required' => true));
        $this->validatorSchema['cardpin'] = new sfValidatorString(array('required' => true));
    }

    parent::bind($taintedValues);
  }

  protected function doUpdateObject($values)
  {
    parent::doUpdateObject($values);

    $this->object->mapValue('address_metro', !empty($values['address_metro']) ? $values['address_metro'] : null);
    $this->object->mapValue('address_street', !empty($values['address_street']) ? $values['address_street'] : null);
    $this->object->mapValue('address_number', isset($values['address_number']) ? $values['address_number'] : null);
    $this->object->mapValue('address_building', isset($values['address_building']) ? $values['address_building'] : null);
    $this->object->mapValue('address_apartment', isset($values['address_apartment']) ? $values['address_apartment'] : null);
  }
}