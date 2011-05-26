<?php

/**
 * SimilarProductProperty filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductPropertyFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'group_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => true)),
      'property_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProductProperty'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'group_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Group'), 'column' => 'id')),
      'property_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProductProperty'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('similar_product_property_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProductProperty';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'group_id'    => 'ForeignKey',
      'property_id' => 'ForeignKey',
    );
  }
}
