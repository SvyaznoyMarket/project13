<?php

/**
 * NewsProductRelation form base class.
 *
 * @method NewsProductRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNewsProductRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'news_id'    => new sfWidgetFormInputHidden(),
      'product_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'news_id'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('news_id')), 'empty_value' => $this->getObject()->get('news_id'), 'required' => false)),
      'product_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('product_id')), 'empty_value' => $this->getObject()->get('product_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('news_product_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NewsProductRelation';
  }

}
