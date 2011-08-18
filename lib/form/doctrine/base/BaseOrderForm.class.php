<?php

/**
 * Order form base class.
 *
 * @method Order getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseOrderForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                => new sfWidgetFormInputHidden(),
      'token'             => new sfWidgetFormInputText(),
      'user_id'           => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'payment_method_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'add_empty' => true)),
      'sum'               => new sfWidgetFormInputText(),
      'person_type'       => new sfWidgetFormChoice(array('choices' => array('individual' => 'individual', 'legal' => 'legal'))),
      'region_id'         => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
      'receipt_type'      => new sfWidgetFormChoice(array('choices' => array('pickup' => 'pickup', 'delivery' => 'delivery'))),
      'delivery_type_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'add_empty' => true)),
      'delivered_at'      => new sfWidgetFormDateTime(),
      'address'           => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'                => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'             => new sfValidatorString(array('max_length' => 64)),
      'user_id'           => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => false)),
      'payment_method_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('PaymentMethod'), 'required' => false)),
      'sum'               => new sfValidatorNumber(array('required' => false)),
      'person_type'       => new sfValidatorChoice(array('choices' => array(0 => 'individual', 1 => 'legal'), 'required' => false)),
      'region_id'         => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'required' => false)),
      'receipt_type'      => new sfValidatorChoice(array('choices' => array(0 => 'pickup', 1 => 'delivery'), 'required' => false)),
      'delivery_type_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('DeliveryType'), 'required' => false)),
      'delivered_at'      => new sfValidatorDateTime(array('required' => false)),
      'address'           => new sfValidatorString(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Order', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('order[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Order';
  }

}
