<?php

/**
 * UserProfile form base class.
 *
 * @method UserProfile getObject() Returns the current form's model object
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseUserProfileForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'source_id'  => new sfWidgetFormInputText(),
      'type'       => new sfWidgetFormChoice(array('choices' => array('vkontakte' => 'vkontakte', 'facebook' => 'facebook', 'twitter' => 'twitter', 'odnoklassniki' => 'odnoklassniki', 'gmail' => 'gmail', 'mailru' => 'mailru', 'live_journal' => 'live_journal', 'yandex' => 'yandex'))),
      'user_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => false)),
      'content'    => new sfWidgetFormTextarea(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'source_id'  => new sfValidatorString(array('max_length' => 255)),
      'type'       => new sfValidatorChoice(array('choices' => array(0 => 'vkontakte', 1 => 'facebook', 2 => 'twitter', 3 => 'odnoklassniki', 4 => 'gmail', 5 => 'mailru', 6 => 'live_journal', 7 => 'yandex'), 'required' => false)),
      'user_id'    => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'))),
      'content'    => new sfValidatorString(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('user_profile[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'UserProfile';
  }

}
