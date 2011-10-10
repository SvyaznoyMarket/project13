<?php

/**
 * Service filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseServiceFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'         => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'          => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'description'   => new sfWidgetFormFilterInput(),
      'work'          => new sfWidgetFormFilterInput(),
      'expendable'    => new sfWidgetFormFilterInput(),
      'is_active'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'core_id'       => new sfWidgetFormFilterInput(),
      'category_list' => new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory')),
    ));

    $this->setValidators(array(
      'token'         => new sfValidatorPass(array('required' => false)),
      'name'          => new sfValidatorPass(array('required' => false)),
      'description'   => new sfValidatorPass(array('required' => false)),
      'work'          => new sfValidatorPass(array('required' => false)),
      'expendable'    => new sfValidatorPass(array('required' => false)),
      'is_active'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'core_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'category_list' => new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'ServiceCategory', 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('service_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function addCategoryListColumnQuery(Doctrine_Query $query, $field, $values)
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
      ->leftJoin($query->getRootAlias().'.ServiceCategoryRelation ServiceCategoryRelation')
      ->andWhereIn('ServiceCategoryRelation.category_id', $values)
    ;
  }

  public function getModelName()
  {
    return 'Service';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'token'         => 'Text',
      'name'          => 'Text',
      'description'   => 'Text',
      'work'          => 'Text',
      'expendable'    => 'Text',
      'is_active'     => 'Boolean',
      'core_id'       => 'Number',
      'category_list' => 'ManyKey',
    );
  }
}
