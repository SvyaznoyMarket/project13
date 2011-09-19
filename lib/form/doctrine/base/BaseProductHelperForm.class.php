<?php

/**
 * ProductHelper form base class.
 *
 * @method ProductHelper getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseProductHelperForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'product_type_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'), 'add_empty' => false)),
      'token'           => new sfWidgetFormInputText(),
      'name'            => new sfWidgetFormInputText(),
      'is_active'       => new sfWidgetFormInputCheckbox(),
      'image'           => new sfWidgetFormInputText(),
      'position'        => new sfWidgetFormInputText(),
      'description'     => new sfWidgetFormTextarea(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'product_type_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProductType'))),
      'token'           => new sfValidatorString(array('max_length' => 255)),
      'name'            => new sfValidatorString(array('max_length' => 255)),
      'is_active'       => new sfValidatorBoolean(array('required' => false)),
      'image'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'position'        => new sfValidatorInteger(array('required' => false)),
      'description'     => new sfValidatorString(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_at'      => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ProductHelper', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('product_helper[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductHelper';
  }

}
