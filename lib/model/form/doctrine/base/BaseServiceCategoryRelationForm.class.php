<?php

/**
 * ServiceCategoryRelation form base class.
 *
 * @method ServiceCategoryRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServiceCategoryRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'category_id' => new sfWidgetFormInputHidden(),
      'service_id'  => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'category_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('category_id')), 'empty_value' => $this->getObject()->get('category_id'), 'required' => false)),
      'service_id'  => new sfValidatorChoice(array('choices' => array($this->getObject()->get('service_id')), 'empty_value' => $this->getObject()->get('service_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('service_category_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServiceCategoryRelation';
  }

}
