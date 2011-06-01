<?php

/**
 * Shop filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseShopFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token'     => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'      => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'region_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Region'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'token'     => new sfValidatorPass(array('required' => false)),
      'name'      => new sfValidatorPass(array('required' => false)),
      'region_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Region'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('shop_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Shop';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'token'     => 'Text',
      'name'      => 'Text',
      'region_id' => 'ForeignKey',
    );
  }
}
