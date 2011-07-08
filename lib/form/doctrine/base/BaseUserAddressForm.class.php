<?php

/**
 * UserAddress form base class.
 *
 * @method UserAddress getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserAddressForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id' => new sfWidgetFormInputHidden(),
      'city_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('City'), 'add_empty' => false)),
      'name'    => new sfWidgetFormInputText(),
      'address' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'user_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('user_id')), 'empty_value' => $this->getObject()->get('user_id'), 'required' => false)),
      'city_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('City'))),
      'name'    => new sfValidatorString(array('max_length' => 255)),
      'address' => new sfValidatorString(),
    ));

    $this->widgetSchema->setNameFormat('user_address[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserAddress';
  }

}
