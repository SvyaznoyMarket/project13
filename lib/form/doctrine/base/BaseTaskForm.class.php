<?php

/**
 * Task form base class.
 *
 * @method Task getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseTaskForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'type'       => new sfWidgetFormChoice(array('choices' => array('project.init' => 'project.init'))),
      'token'      => new sfWidgetFormInputText(),
      'priority'   => new sfWidgetFormInputText(),
      'status'     => new sfWidgetFormChoice(array('choices' => array('run' => 'run', 'pause' => 'pause', 'fail' => 'fail', 'success' => 'success'))),
      'content'    => new sfWidgetFormTextarea(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'type'       => new sfValidatorChoice(array('choices' => array(0 => 'project.init'))),
      'token'      => new sfValidatorString(array('max_length' => 255)),
      'priority'   => new sfValidatorInteger(array('required' => false)),
      'status'     => new sfValidatorChoice(array('choices' => array(0 => 'run', 1 => 'pause', 2 => 'fail', 3 => 'success'), 'required' => false)),
      'content'    => new sfValidatorString(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorDoctrineUnique(array('model' => 'Task', 'column' => array('token')))
    );

    $this->widgetSchema->setNameFormat('task[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Task';
  }

}
