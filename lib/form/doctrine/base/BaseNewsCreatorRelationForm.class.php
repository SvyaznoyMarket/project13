<?php

/**
 * NewsCreatorRelation form base class.
 *
 * @method NewsCreatorRelation getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseNewsCreatorRelationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'news_id'    => new sfWidgetFormInputHidden(),
      'creator_id' => new sfWidgetFormInputHidden(),
    ));

    $this->setValidators(array(
      'news_id'    => new sfValidatorChoice(array('choices' => array($this->getObject()->get('news_id')), 'empty_value' => $this->getObject()->get('news_id'), 'required' => false)),
      'creator_id' => new sfValidatorChoice(array('choices' => array($this->getObject()->get('creator_id')), 'empty_value' => $this->getObject()->get('creator_id'), 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('news_creator_relation[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'NewsCreatorRelation';
  }

}
