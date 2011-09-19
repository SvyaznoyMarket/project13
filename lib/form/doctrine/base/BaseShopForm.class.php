<?php

/**
 * Shop form base class.
 *
 * @method Shop getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseShopForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'core_id'      => new sfWidgetFormInputText(),
      'region_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => false)),
      'token'        => new sfWidgetFormInputText(),
      'name'         => new sfWidgetFormInputText(),
      'latitude'     => new sfWidgetFormInputText(),
      'longitude'    => new sfWidgetFormInputText(),
      'regime'       => new sfWidgetFormInputText(),
      'address'      => new sfWidgetFormInputText(),
      'phonenumbers' => new sfWidgetFormInputText(),
      'photo'        => new sfWidgetFormInputText(),
      'description'  => new sfWidgetFormTextarea(),
      'created_at'   => new sfWidgetFormDateTime(),
      'updated_at'   => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'      => new sfValidatorInteger(array('required' => false)),
      'region_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Region'))),
      'token'        => new sfValidatorString(array('max_length' => 255)),
      'name'         => new sfValidatorString(array('max_length' => 255)),
      'latitude'     => new sfValidatorNumber(array('required' => false)),
      'longitude'    => new sfValidatorNumber(array('required' => false)),
      'regime'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'address'      => new sfValidatorString(array('max_length' => 255)),
      'phonenumbers' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'photo'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description'  => new sfValidatorString(array('required' => false)),
      'created_at'   => new sfValidatorDateTime(),
      'updated_at'   => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Shop', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('shop[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Shop';
  }

}
