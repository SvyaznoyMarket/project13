<?php

/**
 * ProductComment filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseProductCommentFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_parent_id' => new sfWidgetFormFilterInput(),
      'core_user_id'   => new sfWidgetFormFilterInput(),
      'product_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Product'), 'add_empty' => true)),
      'parent_id'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'lft'            => new sfWidgetFormFilterInput(),
      'rgt'            => new sfWidgetFormFilterInput(),
      'level'          => new sfWidgetFormFilterInput(),
      'user_id'        => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'content'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'helpful'        => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'unhelpful'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'is_recomend'    => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'rating'         => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'core_id'        => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'core_parent_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'core_user_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'product_id'     => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Product'), 'column' => 'id')),
      'parent_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'lft'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'rgt'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'level'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_id'        => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('User'), 'column' => 'id')),
      'content'        => new sfValidatorPass(array('required' => false)),
      'helpful'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'unhelpful'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_recomend'    => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'rating'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'core_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('product_comment_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'ProductComment';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'core_parent_id' => 'Number',
      'core_user_id'   => 'Number',
      'product_id'     => 'ForeignKey',
      'parent_id'      => 'Number',
      'lft'            => 'Number',
      'rgt'            => 'Number',
      'level'          => 'Number',
      'user_id'        => 'ForeignKey',
      'content'        => 'Text',
      'helpful'        => 'Number',
      'unhelpful'      => 'Number',
      'is_recomend'    => 'Boolean',
      'rating'         => 'Number',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'core_id'        => 'Number',
    );
  }
}
