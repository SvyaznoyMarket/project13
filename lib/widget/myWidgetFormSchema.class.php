<?php

class myWidgetFormSchema extends sfWidgetFormSchema
{
  public function render($name, $values = array(), $attributes = array(), $errors = array())
  {
    if (null === $values)
    {
      $values = array();
    }

    if (!is_array($values) && !$values instanceof ArrayAccess)
    {
      throw new InvalidArgumentException('You must pass an array of values to render a widget schema');
    }

    $formFormat = $this->getFormFormatter();

    $rows = array();
    $hiddenRows = array();
    $errorRows = array();

    // render each field
    foreach ($this->positions as $name)
    {
      $widget = $this[$name];
      $value = isset($values[$name]) ? $values[$name] : null;
      $error = isset($errors[$name]) ? $errors[$name] : array();
      $widgetAttributes = isset($attributes[$name]) ? $attributes[$name] : array();

      if ($widget instanceof sfWidgetForm && $widget->isHidden())
      {
        $hiddenRows[] = $this->renderField($name, $value, $widgetAttributes);
      }
      else
      {
        $field = $this->renderField($name, $value, $widgetAttributes, $error);

        // don't add a label tag and errors if we embed a form schema
        $label = $widget instanceof sfWidgetFormSchema ? $this->getFormFormatter()->generateLabelName($name) : $this->getFormFormatter()->generateLabel($name);
        $error = $widget instanceof sfWidgetFormSchema ? array() : $error;

        // добавлен последний аргумент "дополнительные параметры"
        $rows[] = $formFormat->formatRow($label, $field, $error, $this->getHelp($name), null, array(
          'name' => $name,
        ));
      }
    }

    if ($rows)
    {
      // insert hidden fields in the last row
      for ($i = 0, $max = count($rows); $i < $max; $i++)
      {
        $rows[$i] = strtr($rows[$i], array('%hidden_fields%' => $i == $max - 1 ? implode("\n", $hiddenRows) : ''));
      }
    }
    else
    {
      // only hidden fields
      $rows[0] = implode("\n", $hiddenRows);
    }

    return $this->getFormFormatter()->formatErrorRow($this->getGlobalErrors($errors)).implode('', $rows);
  }
}