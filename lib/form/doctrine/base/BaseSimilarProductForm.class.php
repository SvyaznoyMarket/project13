<?php

/**
 * SimilarProduct form base class.
 *
 * @method SimilarProduct getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseSimilarProductForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'master_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MasterProduct'), 'add_empty' => false)),
      'slave_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('SlaveProduct'), 'add_empty' => false)),
      'group_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Group'), 'add_empty' => false)),
      'is_manual' => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'master_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MasterProduct'))),
      'slave_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('SlaveProduct'))),
      'group_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Group'))),
      'is_manual' => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('similar_product[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'SimilarProduct';
  }

}
