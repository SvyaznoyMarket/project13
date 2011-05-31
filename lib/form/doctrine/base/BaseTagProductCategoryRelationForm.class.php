<?php

/**
 * TagProductCategoryRelation form base class.
 *
 * @method TagProductCategoryRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTagProductCategoryRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'tag_id'              => new sfWidgetFormInputHidden(),
      'product_category_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'tag_id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('tag_id')), 'empty_value' => $this->getObject()->get('tag_id'), 'required' => false)),
      'product_category_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_category_id')), 'empty_value' => $this->getObject()->get('product_category_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_product_category_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagProductCategoryRelation';
  }

}
