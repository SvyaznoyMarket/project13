<?php

class UnitellerPaymentForm extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['Shop_IDP'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Shop_IDP'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['Order_IDP'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Order_IDP'] = new sfValidatorString(array('required' => true, 'max_length' => 127));

    $this->widgetSchema['Subtotal_P'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Subtotal_P'] = new sfValidatorNumber(array('min' => 0));

    $this->widgetSchema['Signature'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Signature'] = new sfValidatorString();

    $this->widgetSchema['URL_RETURN'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['URL_RETURN'] = new sfValidatorString();
  }

  public function getUrl()
  {
    return $this->getOption('url');
  }

  public function getSum()
  {
    return $this->getDefault('Subtotal_P');
  }
}