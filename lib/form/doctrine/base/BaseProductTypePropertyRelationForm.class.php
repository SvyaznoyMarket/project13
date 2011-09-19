<?php

/**
 * ProductTypePropertyRelation form base class.
 *
 * @method ProductTypePropertyRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductTypePropertyRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'product_type_id' => new sfWidgetFormInputHidden(),
      'property_id'     => new sfWidgetFormInputHidden(),
      'group_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'name'            => new sfWidgetFormInputText(),
      'position'        => new sfWidgetFormInputText(),
      'group_position'  => new sfWidgetFormInputText(),
      'view_show'       => new sfWidgetFormInputCheckbox(),
      'view_list'       => new sfWidgetFormInputCheckbox(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'product_type_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_type_id')), 'empty_value' => $this->getObject()->get('product_type_id'), 'required' => false)),
      'property_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('property_id')), 'empty_value' => $this->getObject()->get('property_id'), 'required' => false)),
      'group_id'        => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'required' => false)),
      'name'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'position'        => new sfValidatorInteger(array('required' => false)),
      'group_position'  => new sfValidatorInteger(array('required' => false)),
      'view_show'       => new sfValidatorBoolean(array('required' => false)),
      'view_list'       => new sfValidatorBoolean(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('product_type_property_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductTypePropertyRelation';
  }

}
