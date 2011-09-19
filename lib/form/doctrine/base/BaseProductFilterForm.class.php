<?php

/**
 * ProductFilter form base class.
 *
 * @method ProductFilter getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductFilterForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'name'        => new sfWidgetFormInputText(),
      'type'        => new sfWidgetFormChoice(array('choices' => array('choice' => 'choice', 'range' => 'range'))),
      'group_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => false)),
      'property_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => false)),
      'is_multiple' => new sfWidgetFormInputCheckbox(),
      'position'    => new sfWidgetFormInputText(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'type'        => new sfValidatorChoice(array('choices' => array(0 => 'choice', 1 => 'range'))),
      'group_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'))),
      'property_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Property'))),
      'is_multiple' => new sfValidatorBoolean(array('required' => false)),
      'position'    => new sfValidatorInteger(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('product_filter[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductFilter';
  }

}
