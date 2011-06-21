<?php

/**
 * ProductCategoryTypeRelation form base class.
 *
 * @method ProductCategoryTypeRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductCategoryTypeRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'product_category_id' => new sfWidgetFormInputHidden(),
      'product_type_id'     => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'product_category_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_category_id')), 'empty_value' => $this->getObject()->get('product_category_id'), 'required' => false)),
      'product_type_id'     => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_type_id')), 'empty_value' => $this->getObject()->get('product_type_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('product_category_type_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductCategoryTypeRelation';
  }

}
