<?php

/**
 * SimilarProductGroup form base class.
 *
 * @method SimilarProductGroup getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'product_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'), 'add_empty' => false)),
      'name'            => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'product_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'))),
      'name'            => new sfValidatorString(array('max_length' => 255)),
    ));

    $this->widgetSchema->setNameFormat('similar_product_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProductGroup';
  }

}
