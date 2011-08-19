<?php

class OrderStep2Form extends BaseOrderForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['payment_method_id'] = new sfWidgetFormDoctrineChoice(array(
      'model'     => 'PaymentMethod',
      'add_empty' => false,
      'expanded'  => true,
    ));
    $this->widgetSchema['payment_method_id']->setLabel('Варианты оплаты');
    $this->validatorSchema['payment_method_id'] = new sfValidatorDoctrineChoice(array('model' => 'PaymentMethod', 'required' => true));


    $this->useFields(array(
      'payment_method_id',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);
  }
}