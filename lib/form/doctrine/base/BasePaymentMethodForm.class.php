<?php

/**
 * PaymentMethod form base class.
 *
 * @method PaymentMethod getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePaymentMethodForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'core_id'     => new sfWidgetFormInputText(),
      'token'       => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'description' => new sfWidgetFormTextarea(),
      'is_active'   => new sfWidgetFormInputCheckbox(),
      'is_legal'    => new sfWidgetFormInputCheckbox(),
      'is_personal' => new sfWidgetFormInputCheckbox(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'     => new sfValidatorInteger(array('required' => false)),
      'token'       => new sfValidatorString(array('max_length' => 255)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'description' => new sfValidatorString(array('max_length' => 500)),
      'is_active'   => new sfValidatorBoolean(array('required' => false)),
      'is_legal'    => new sfValidatorBoolean(array('required' => false)),
      'is_personal' => new sfValidatorBoolean(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'PaymentMethod', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('payment_method[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'PaymentMethod';
  }

}
