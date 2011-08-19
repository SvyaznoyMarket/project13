<?php

class myFormField extends sfFormField
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
}