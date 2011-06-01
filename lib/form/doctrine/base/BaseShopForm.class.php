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
      'id'        => new sfWidgetFormInputHidden(),
      'token'     => new sfWidgetFormInputText(),
      'name'      => new sfWidgetFormInputText(),
      'region_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'token'     => new sfValidatorString(array('max_length' => 255)),
      'name'      => new sfValidatorString(array('max_length' => 255)),
      'region_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Region'))),
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
