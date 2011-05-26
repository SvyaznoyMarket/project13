<?php

/**
 * SimilarProduct filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'master_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MasterProduct'), 'add_empty' => true)),
      'slave_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SlaveProduct'), 'add_empty' => true)),
      'group_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'is_manual' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'master_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('MasterProduct'), 'column' => 'id')),
      'slave_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SlaveProduct'), 'column' => 'id')),
      'group_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Group'), 'column' => 'id')),
      'is_manual' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('similar_product_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProduct';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'master_id' => 'ForeignKey',
      'slave_id'  => 'ForeignKey',
      'group_id'  => 'ForeignKey',
      'is_manual' => 'Boolean',
    );
  }
}
