<?php

/**
 * ProductRatingTypeProperty form base class.
 *
 * @method ProductRatingTypeProperty getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductRatingTypePropertyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'type_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Type'), 'add_empty' => false)),
      'token'      => new sfWidgetFormInputText(),
      'name'       => new sfWidgetFormInputText(),
      'position'   => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'type_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Type'))),
      'token'      => new sfValidatorString(array('max_length' => 255)),
      'name'       => new sfValidatorString(array('max_length' => 255)),
      'position'   => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ProductRatingTypeProperty', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('product_rating_type_property[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductRatingTypeProperty';
  }

}
