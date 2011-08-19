<?php

class myFormFieldSchema extends sfFormFieldSchema
{
  public function renderRow($attributes = array(), $label = null, $help = null)
  {
    if (null === $this->parent)
    {
      throw new LogicException(sprintf('Unable to render the row for "%s".', $this->name));
    }

    $field = $this->parent->getWidget()->renderField($this->name, $this->value, !is_array($attributes) ? array() : $attributes, $this->error);

    $error = $this->error instanceof sfValidatorErrorSchema ? $this->error->getGlobalErrors() : $this->error;

    $help = null === $help ? $this->parent->getWidget()->getHelp($this->name) : $help;

    return strtr($this->parent->getWidget()->getFormFormatter()->formatRow($this->renderLabel($label), $field, $error, $help, null, array('name' => $this->name)), array('%hidden_fields%' => ''));
  }

  public function offsetGet($name)
  {
    if (!isset($this->fields[$name]))
    {
      if (null === $widget = $this->widget[$name])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      $error = isset($this->error[$name]) ? $this->error[$name] : null;

      if ($widget instanceof sfWidgetFormSchema)
      {
        $class = 'myFormFieldSchema';

        if ($error && !$error instanceof sfValidatorErrorSchema)
        {
          $error = new sfValidatorErrorSchema($error->getValidator(), array($error));
        }
      }
      else
      {
        $class = 'myFormField';
      }

      $this->fields[$name] = new $class($widget, $this, $name, isset($this->value[$name]) ? $this->value[$name] : null, $error);
    }

    return $this->fields[$name];
  }
}
