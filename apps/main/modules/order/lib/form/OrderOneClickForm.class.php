<?php

class OrderOneClickForm extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $this->disableCSRFProtection();

    $this->widgetSchema['delivery_type_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['delivery_type_id'] = new sfValidatorInteger(array('required' => true));

    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_first_name']->setLabel('Имя получателя:');
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['recipient_phonenumbers'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_phonenumbers']->setLabel('Телефон для связи:');
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['product_quantity'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['product_quantity'] = new sfValidatorInteger(array('min' => 1, 'required' => true));
    $this->widgetSchema['product_quantity']->setDefault($this->getOption('quantity', 1));

    $this->widgetSchema['delivered_at'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['delivered_at'] = new sfValidatorDateTime(array('required' => false));

    $this->widgetSchema['shop_id'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['shop_id'] = new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Shop'), 'required' => false));

    if ($user = $this->getOption('user', false))
    {
      $this->setDefaults(array(
        'recipient_first_name'   => $user->first_name,
        'recipient_phonenumbers' => $user->phonenumber,
      ));
    }

    $this->useFields(array(
      'recipient_first_name',
      'recipient_phonenumbers',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }
}