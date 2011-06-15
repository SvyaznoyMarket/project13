<?php

/**
 * ProductHelperAnswer filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductHelperAnswerFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'question_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Question'), 'add_empty' => true)),
      'name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_active'   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'position'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'condition'   => new sfWidgetFormChoice(array('choices' => array('' => '', 'and' => 'and', 'or' => 'or'))),
    ));

    $this->setValidators(array(
      'question_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Question'), 'column' => 'id')),
      'name'        => new sfValidatorPass(array('required' => false)),
      'is_active'   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'position'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'condition'   => new sfValidatorChoice(array('required' => false, 'choices' => array('and' => 'and', 'or' => 'or'))),
    ));

    $this->widgetSchema->setNameFormat('product_helper_answer_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductHelperAnswer';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'question_id' => 'ForeignKey',
      'name'        => 'Text',
      'is_active'   => 'Boolean',
      'position'    => 'Number',
      'condition'   => 'Enum',
    );
  }
}
