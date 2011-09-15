<?php

/**
 * Tag filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseTagFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'  => new sfWidgetFormFilterInput(),
      'group_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'token'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'core_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'group_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Group'), 'column' => 'id')),
      'token'    => new sfValidatorPass(array('required' => false)),
      'name'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('tag_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Tag';
  }

  public function getFields()
  {
    return array(
      'id'       => 'Number',
      'core_id'  => 'Number',
      'group_id' => 'ForeignKey',
      'token'    => 'Text',
      'name'     => 'Text',
    );
  }
}
