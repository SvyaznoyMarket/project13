<?php

/**
 * Page filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BasePageFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'root_id'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'         => new sfWidgetFormFilterInput(),
      'rgt'         => new sfWidgetFormFilterInput(),
      'level'       => new sfWidgetFormFilterInput(),
      'token'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'title'       => new sfWidgetFormFilterInput(),
      'header'      => new sfWidgetFormFilterInput(),
      'keywords'    => new sfWidgetFormFilterInput(),
      'description' => new sfWidgetFormFilterInput(),
      'content'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'root_id'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'       => new sfValidatorPass(array('required' => false)),
      'name'        => new sfValidatorPass(array('required' => false)),
      'title'       => new sfValidatorPass(array('required' => false)),
      'header'      => new sfValidatorPass(array('required' => false)),
      'keywords'    => new sfValidatorPass(array('required' => false)),
      'description' => new sfValidatorPass(array('required' => false)),
      'content'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('page_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Page';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'root_id'     => 'Number',
      'lft'         => 'Number',
      'rgt'         => 'Number',
      'level'       => 'Number',
      'token'       => 'Text',
      'name'        => 'Text',
      'title'       => 'Text',
      'header'      => 'Text',
      'keywords'    => 'Text',
      'description' => 'Text',
      'content'     => 'Text',
    );
  }
}
