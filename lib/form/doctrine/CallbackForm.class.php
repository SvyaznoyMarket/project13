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
  

  protected function updateNameColumn($value)
  {
    if (empty($value))
    {
      $value = $this->getValue('callback');
    }

    return $value;
  }
}
