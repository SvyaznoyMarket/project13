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
    ));

    $this->setValidators(array(
      'master_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('MasterProduct'), 'column' => 'id')),
      'slave_id'  => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('SlaveProduct'), 'column' => 'id')),
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
    );
  }
}
