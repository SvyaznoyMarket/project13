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
      'id'         => new sfWidgetFormInputHidden(),
      'order_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Order'), 'add_empty' => false)),
      'product_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => false)),
      'price'      => new sfWidgetFormInputText(),
      'quantity'   => new sfWidgetFormInputText(),
      'position'   => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'order_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Order'))),
      'product_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Product'))),
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
