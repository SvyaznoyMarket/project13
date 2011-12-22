<?php

class OrderOneClickForm extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');

    $this->disableCSRFProtection();

    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_first_name']->setLabel('Имя получателя:');
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['recipient_phonenumbers'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_phonenumbers']->setLabel('Телефон для связи:');
    $this->validatorSchema['recipient_phonenumbers'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['product_quantity'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['product_quantity'] = new sfValidatorInteger(array('min' => 1, 'required' => true));
    $this->widgetSchema['product_quantity']->setDefault(1);

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