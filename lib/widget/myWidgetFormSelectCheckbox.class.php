<?php

class myWidgetFormSelectCheckbox extends sfWidgetFormSelectCheckbox
{
  protected function formatChoices($name, $value, $choices, $attributes)
  {
    $inputs = array();
    foreach ($choices as $key => $option)
    {
      $baseAttributes = array(
        'name'  => $name,
        'type'  => 'checkbox',
        'value' => self::escapeOnce($key),
        'id'    => $id = $this->generateId($name, self::escapeOnce($key)),
      );

      if ((is_array($value) && in_array(strval($key), $value)) || strval($key) == strval($value))
      {
        $baseAttributes['checked'] = 'checked';
      }

      $inputs[$id] = array(
        'label' => $this->renderContentTag('label', self::escapeOnce($option), array('for' => $id)),
        'input' => $this->renderTag('input', array_merge($baseAttributes, $attributes)),
      );
    }

    return call_user_func($this->getOption('formatter'), $this, $inputs);
  }

}