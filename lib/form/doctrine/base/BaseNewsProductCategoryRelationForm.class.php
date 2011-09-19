<?php

/**
 * NewsProductCategoryRelation form base class.
 *
 * @method NewsProductCategoryRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNewsProductCategoryRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'news_id'             => new sfWidgetFormInputHidden(),
      'product_category_id' => new sfWidgetFormInputHidden(),
      'created_at'          => new sfWidgetFormDateTime(),
      'updated_at'          => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'news_id'             => new sfValidatorChoice(array('choices' => array($this->getObject()->get('news_id')), 'empty_value' => $this->getObject()->get('news_id'), 'required' => false)),
      'product_category_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_category_id')), 'empty_value' => $this->getObject()->get('product_category_id'), 'required' => false)),
      'created_at'          => new sfValidatorDateTime(),
      'updated_at'          => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('news_product_category_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NewsProductCategoryRelation';
  }

}
