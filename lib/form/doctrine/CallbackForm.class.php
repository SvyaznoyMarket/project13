<?php

/**
 * Callback form.
 *
 * @package    enter
 * @subpackage form
 * @author     Связной Маркет
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CallbackForm extends BaseCallbackForm
{
  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();

    $this->widgetSchema['name']->setLabel('Ваше имя');
    $this->widgetSchema['name']->setAttribute('class', 'bInputBlock__eInput');

    $this->widgetSchema['email']->setLabel('Ваша электронная почта');
    $this->widgetSchema['email']->setAttribute('class', 'bInputBlock__eInput');

    $this->widgetSchema['theme']->setLabel('Тема');
    $this->widgetSchema['theme']->setAttribute('class', 'bInputBlock__eInput');

    $this->widgetSchema['text']->setLabel('Сообщение');
    $this->widgetSchema['text']->setAttribute('class', 'bInputBlock__eTextarea');
    
    $this->useFields(array(
      'name',
      'email',
      'theme',
      'text',
    ));

    $this->widgetSchema->setNameFormat('callback[%s]');
  }

  public function setup()
  {
    parent::setup();
      
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'core_id'     => new sfWidgetFormInputText(),
      'category_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'add_empty' => true)),
      'user_id'     => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'add_empty' => true)),
      'name'        => new sfWidgetFormInputText(),
      'email'       => new sfWidgetFormInputText(),
      'theme'       => new sfWidgetFormInputText(),
      'text'        => new sfWidgetFormTextarea(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    
    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'core_id'     => new sfValidatorInteger(array('required' => false)),
      'category_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Category'), 'required' => false)),
      'user_id'     => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('User'), 'required' => true)),
      'name'        => new sfValidatorString(array('max_length' => 255, 'required' => true, 'trim'=>true)),
      'email'       => new sfValidatorString(array('max_length' => 255, 'required' => true, 'trim'=>true)),
      'theme'       => new sfValidatorString(array('max_length' => 255, 'required' => true, 'trim'=>true)),
      'text'        => new sfValidatorString(array('required' => true, 'trim'=>true)),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('callback[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

  }  
  
  public function getModelName()
  {
    return 'Callback';
  }  
  

  protected function updateNameColumn($value)
  {
    if (empty($value))
    {
      $value = $this->getValue('callback');
    }

    return $value;
  }
}
