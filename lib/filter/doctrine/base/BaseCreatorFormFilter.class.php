<?php

/**
 * Creator filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseCreatorFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'core_id'    => new sfWidgetFormFilterInput(),
      'token'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'news_list'  => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'News')),
    ));

    $this->setValidators(array(
      'core_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'token'      => new sfValidatorPass(array('required' => false)),
      'name'       => new sfValidatorPass(array('required' => false)),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'news_list'  => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'News', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('creator_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addNewsListColumnQuery(Doctrine_Query $query, $field, $values)
  {
    if (!is_array($values))
    {
      $values = array($values);
    }

    if (!count($values))
    {
      return;
    }

    $query
      ->leftJoin($query->getRootAlias().'.NewsCreatorRelation NewsCreatorRelation')
      ->andWhereIn('NewsCreatorRelation.news_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Creator';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'core_id'    => 'Number',
      'token'      => 'Text',
      'name'       => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'news_list'  => 'ManyKey',
    );
  }
}
