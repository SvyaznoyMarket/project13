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

    $this->widgetSchema['recipient_last_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_last_name']->setLabel('Фамилия');
    $this->validatorSchema['recipient_last_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->widgetSchema['recipient_first_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_first_name']->setLabel('Имя');
    $this->validatorSchema['recipient_first_name'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    $this->widgetSchema['recipient_middle_name'] = new sfWidgetFormInputText();
    $this->widgetSchema['recipient_middle_name']->setLabel('Отчество');
    $this->validatorSchema['recipient_middle_name'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

    $this->useFields(array(
      'payment_method_id',
      'recipient_last_name',
      'recipient_first_name',
      'recipient_middle_name',
    ));

    $this->widgetSchema->setNameFormat('order[%s]');
  }

  public function bind(array $taintedValues = null, array $taintedFiles = null)
  {
    parent::bind($taintedValues, $taintedFiles);
  }
}