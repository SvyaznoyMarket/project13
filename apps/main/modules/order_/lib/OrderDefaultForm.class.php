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

    if (!$regions = $this->getOption('regions', false))
    {
      throw new Exception('Regions not provided');
    }

    if (!$deliveryTypes = $this->getOption('deliveryTypes', false))
    {
      throw new Exception('Delivery types not provided');
    }

    // region_id
    $this->widgetSchema['region_id'] = new myWidgetFormComponent(array('component' => array('order_', 'field_region_id'), 'component_param' => array('regionList' => $regions)));
    $this->validatorSchema['region_id'] = new sfValidatorChoice(array('choices' => $regions->toValueArray('id'), 'required' => true));
    $this->widgetSchema['region_id']->setLabel('В каком городе вы будете получать заказ?');

    // delivery_type_id
    $this->widgetSchema['delivery_type_id'] = new myWidgetFormComponent(array(
      'component' => array('order_', 'field_delivery_type_id'),
      'component_param' => array('deliveryTypes' => $deliveryTypes),
    ));
    $this->validatorSchema['delivery_type_id'] = new sfValidatorChoice(array('choices' => array_map(function($i) { return $i->getToken(); }, $deliveryTypes), 'required' => true), array('required' => 'Выберите способ получения заказа'));
    $this->widgetSchema['delivery_type_id']->setLabel('Выберите способ получения заказа:');

    // recipient_first_name
    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Укажите кто будет получать заказ'));
    $this->widgetSchema['recipient_first_name']->setLabel('Имя получателя:');

    // recipient_phonenumbers
    $this->widgetSchema['recipient_phonenumbers'] = new sfWidgetFormInputText();
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Укажите телефон для связи'));
    $this->widgetSchema['recipient_phonenumbers']->setLabel('Мобильный телефон для связи:');


    // is_receive_sms
    $this->widgetSchema['is_receive_sms'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['is_receive_sms'] = new sfValidatorBoolean();
    $this->widgetSchema['is_receive_sms']->setLabel('Я хочу получать СМС уведомления об изменении статуса заказа');

    // address
    $this->widgetSchema['address'] = new sfWidgetFormInputText();
    $this->validatorSchema['address'] = new sfValidatorString(array('required' => true), array('required' => 'Укажите адрес доставки'));
    $this->widgetSchema['address']->setLabel('Адрес доставки:');

    // extra
    $this->widgetSchema['extra'] = new sfWidgetFormTextarea();
    $this->validatorSchema['extra'] = new sfValidatorString(array('required' => false));
    $this->widgetSchema['extra']->setLabel('Комментарии:');

    // payment_method_id
    $choices = array();
    $this->widgetSchema['payment_method_id'] = new myWidgetFormComponent(array(
      'component' => array('order_', 'field_payment_method_id'),
      'component_param' => array(),
    ));
    $this->validatorSchema['payment_method_id'] = new sfValidatorDoctrineChoice(array('model' => 'PaymentMethod', 'required' => true), array('required' => 'Выберите способ оплаты'));
    $this->widgetSchema['payment_method_id']->setLabel('Выберите способ оплаты:');

    // agreed
    $this->widgetSchema['agreed'] = new sfWidgetFormInputCheckbox();
    $this->validatorSchema['agreed'] = new sfValidatorBoolean(array('required' => true), array('required' => 'Пожалуйста, ознакомьтесь с условиями продажи и правовой информацией и поставьте галочку'));
    $this->widgetSchema['agreed']->setLabel('Я ознакомлен и согласен с «Условиями продажи» и «Правовой информацией»');

    // использовать поля
    $this->useFields(array(
      'region_id',
      'delivery_type_id',
      'recipient_first_name',
      'recipient_phonenumbers',
      'is_receive_sms',
      'address',
      'extra',
      'payment_method_id',
      'agreed',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }
}