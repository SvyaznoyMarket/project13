<?php

/**
 * TagGroup form base class.
 *
 * @method TagGroup getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTagGroupForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'core_id'  => new sfWidgetFormInputText(),
      'token'    => new sfWidgetFormInputText(),
      'name'     => new sfWidgetFormInputText(),
      'type'     => new sfWidgetFormInputText(),
      'position' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'  => new sfValidatorInteger(array('required' => false)),
      'token'    => new sfValidatorString(array('max_length' => 255)),
      'name'     => new sfValidatorString(array('max_length' => 255)),
      'type'     => new sfValidatorPass(array('required' => false)),
      'position' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'TagGroup', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('tag_group[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'TagGroup';
  }

}
