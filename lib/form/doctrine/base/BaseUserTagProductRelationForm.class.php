<?php

/**
 * UserTagProductRelation form base class.
 *
 * @method UserTagProductRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserTagProductRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tag_id'     => new sfWidgetFormInputHidden(),
      'product_id' => new sfWidgetFormInputHidden(),
      'position'   => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'tag_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('tag_id')), 'empty_value' => $this->getObject()->get('tag_id'), 'required' => false)),
      'product_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_id')), 'empty_value' => $this->getObject()->get('product_id'), 'required' => false)),
      'position'   => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('user_tag_product_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserTagProductRelation';
  }

}
