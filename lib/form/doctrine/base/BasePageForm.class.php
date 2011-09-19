<?php

/**
 * Page form base class.
 *
 * @method Page getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BasePageForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'root_id'     => new sfWidgetFormInputText(),
      'lft'         => new sfWidgetFormInputText(),
      'rgt'         => new sfWidgetFormInputText(),
      'level'       => new sfWidgetFormInputText(),
      'token'       => new sfWidgetFormInputText(),
      'name'        => new sfWidgetFormInputText(),
      'title'       => new sfWidgetFormTextarea(),
      'header'      => new sfWidgetFormTextarea(),
      'keywords'    => new sfWidgetFormTextarea(),
      'description' => new sfWidgetFormTextarea(),
      'content'     => new sfWidgetFormTextarea(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'root_id'     => new sfValidatorInteger(),
      'lft'         => new sfValidatorInteger(array('required' => false)),
      'rgt'         => new sfValidatorInteger(array('required' => false)),
      'level'       => new sfValidatorInteger(array('required' => false)),
      'token'       => new sfValidatorString(array('max_length' => 255)),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'title'       => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'header'      => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'keywords'    => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'description' => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'content'     => new sfValidatorString(array('required' => false)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Page', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('page[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Page';
  }

}
