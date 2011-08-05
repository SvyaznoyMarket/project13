<?php

class UserOrder extends BaseUserData
{
  function __construct($parameters = array())
  {
    $parameters = myToolkit::arrayDeepMerge(array('forms' => array(), ), $parameters);
    $this->parameterHolder = new sfParameterHolder();
    $this->parameterHolder->add($parameters);
  }

  public function getForm(integer $step)
  {
    $forms = $this->parameterHolder->get('forms');

    if (!isset($forms[$step]) || !is_array($forms[$step]))
    {
      $forms[$step] = array();
    }

    return $forms[$step];
  }

  public function setForm(integer $step, BaseForm $form)
  {
    $forms = $this->parameterHolder->get('forms');

    $forms[$step] = $form->getValues();

    $this->parameterHolder->set('forms', $forms);
  }
}