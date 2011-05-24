<?php

/**
 * ProductPropertyRelation form base class.
 *
 * @method ProductPropertyRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductPropertyRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'name'         => new sfWidgetFormInputText(),
      'property_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => false)),
      'product_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => false)),
      'option_id'    => new sfWidgetFormInputText(),
      'value_string' => new sfWidgetFormInputText(),
      'value_text'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'property_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Property'))),
      'product_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Product'))),
      'option_id'    => new sfValidatorInteger(array('required' => false)),
      'value_string' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'value_text'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_property_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductPropertyRelation';
  }

}
