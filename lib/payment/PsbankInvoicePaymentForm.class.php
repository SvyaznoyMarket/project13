<?php

class PsbankInvoicePaymentForm extends BaseForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['ContractorID'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['ContractorID'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['InvoiceID'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['InvoiceID'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['Sum'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Sum'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['PayDescription'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['PayDescription'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['AdditionalInfo'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['AdditionalInfo'] = new sfValidatorString(array('required' => true));

    $this->widgetSchema['Signature'] = new sfWidgetFormInputHidden();
    $this->validatorSchema['Signature'] = new sfValidatorString(array('required' => true));
  }
}