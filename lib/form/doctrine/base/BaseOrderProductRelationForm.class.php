<?php

/**
 * OrderProductRelation form base class.
 *
 * @method OrderProductRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseOrderProductRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'order_id'   => new sfWidgetFormInputHidden(),
      'product_id' => new sfWidgetFormInputHidden(),
      'price'      => new sfWidgetFormInputText(),
      'quantity'   => new sfWidgetFormInputText(),
      'position'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'order_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('order_id')), 'empty_value' => $this->getObject()->get('order_id'), 'required' => false)),
      'product_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_id')), 'empty_value' => $this->getObject()->get('product_id'), 'required' => false)),
      'price'      => new sfValidatorNumber(array('required' => false)),
      'quantity'   => new sfValidatorInteger(),
      'position'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('order_product_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'OrderProductRelation';
  }

}
