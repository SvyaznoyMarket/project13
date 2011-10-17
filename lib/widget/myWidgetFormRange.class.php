<?php

class myWidgetFormRange extends sfWidgetFormInput
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('value_from');
    $this->addRequiredOption('value_to');

    $this->addOption('label_from', 'от');
    $this->addOption('label_to', 'до');

    $this->addOption('template', <<<EOF
%label_from% %value_from% %label_to% %value_to%
EOF
    );
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $values = array_merge(array('from' => '', 'to' => ''), is_array($value) ? $value : array());

    return strtr($this->getOption('template'), array(
      '%value_from%' => $this->renderTag('input', array('type' => 'hidden', 'name' => $name.'[from]', 'value' => $values['from'])),
      '%value_to%'   => $this->renderTag('input', array('type' => 'hidden', 'name' => $name.'[to]', 'value' => $values['to'])),
      '%label_from%' => $this->getOption('label_from') ? $this->renderContentTag('label', $this->getOption('label_from'), array('for' => $this->generateId($name.'[from]'))) : '',
      '%label_to%'   => $this->getOption('label_to') ? $this->renderContentTag('label', $this->getOption('label_to'), array('for' => $this->generateId($name.'[to]'))) : '',
    ));
  }
}