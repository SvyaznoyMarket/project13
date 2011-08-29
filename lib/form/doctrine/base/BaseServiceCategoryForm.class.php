<?php

/**
 * ServiceCategory form base class.
 *
 * @method ServiceCategory getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseServiceCategoryForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'root_id'   => new sfWidgetFormInputText(),
      'lft'       => new sfWidgetFormInputText(),
      'rgt'       => new sfWidgetFormInputText(),
      'level'     => new sfWidgetFormInputText(),
      'token'     => new sfWidgetFormInputText(),
      'name'      => new sfWidgetFormInputText(),
      'is_active' => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'root_id'   => new sfValidatorInteger(),
      'lft'       => new sfValidatorInteger(array('required' => false)),
      'rgt'       => new sfValidatorInteger(array('required' => false)),
      'level'     => new sfValidatorInteger(array('required' => false)),
      'token'     => new sfValidatorString(array('max_length' => 255)),
      'name'      => new sfValidatorString(array('max_length' => 255)),
      'is_active' => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'ServiceCategory', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('service_category[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ServiceCategory';
  }

}
