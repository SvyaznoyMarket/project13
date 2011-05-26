<?php

/**
 * SimilarProductProperty form base class.
 *
 * @method SimilarProductProperty getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductPropertyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'group_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => false)),
      'property_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductProperty'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'group_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'))),
      'property_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProductProperty'))),
    ));

    $this->widgetSchema->setNameFormat('similar_product_property[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProductProperty';
  }

}
