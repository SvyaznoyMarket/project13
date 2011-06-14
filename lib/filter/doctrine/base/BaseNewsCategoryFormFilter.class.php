<?php

/**
 * NewsCategory filter form base class.
 *
 * @package    enter
 * @subpackage filter
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseNewsCategoryFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'token' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'token' => new sfValidatorPass(array('required' => false)),
      'name'  => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('news_category_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NewsCategory';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Number',
      'token' => 'Text',
      'name'  => 'Text',
    );
  }
}
