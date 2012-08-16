<?php

class PsbankPaymentForm extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['AMOUNT'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['AMOUNT'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['CURRENCY'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['CURRENCY'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['ORDER'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['ORDER'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['DESC'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['DESC'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['TERMINAL'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['TERMINAL'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['TRTYPE'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['TRTYPE'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['MERCH_NAME'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['MERCH_NAME'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['MERCHANT'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['MERCHANT'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['EMAIL'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['EMAIL'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['TIMESTAMP'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['TIMESTAMP'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['NONCE'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['NONCE'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['BACKREF'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['BACKREF'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['P_SIGN'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['P_SIGN'] = new sfValidatorString(array('required' => true));
  }
}