<?php

/**
 * ProductGroupPropertyRelation form base class.
 *
 * @method ProductGroupPropertyRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductGroupPropertyRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'property_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Property'), 'add_empty' => false)),
      'product_group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductGroup'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'property_id'      => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Property'))),
      'product_group_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProductGroup'))),
    ));

    $this->widgetSchema->setNameFormat('product_group_property_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductGroupPropertyRelation';
  }

}
