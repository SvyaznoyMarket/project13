<?php

/**
 * ProductTypePropertyGroupRelation form base class.
 *
 * @method ProductTypePropertyGroupRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductTypePropertyGroupRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'product_type_id'   => new sfWidgetFormInputHidden(),
      'property_group_id' => new sfWidgetFormInputHidden(),
      'created_at'        => new sfWidgetFormDateTime(),
      'updated_at'        => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'product_type_id'   => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_type_id')), 'empty_value' => $this->getObject()->get('product_type_id'), 'required' => false)),
      'property_group_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('property_group_id')), 'empty_value' => $this->getObject()->get('property_group_id'), 'required' => false)),
      'created_at'        => new sfValidatorDateTime(),
      'updated_at'        => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('product_type_property_group_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductTypePropertyGroupRelation';
  }

}
