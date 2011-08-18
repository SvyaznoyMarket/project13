<?php

class sfWidgetFormSchemaFormatterDefault extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li %position% class=\"form-row\">\n %label%\n %error%\n  %field%%help%\n%hidden_fields%</li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = "<br /><span class=\"help\">%help%</span>",
    $decoratorFormat = "<ul class=\"form-content\">\n  %content%</ul>";

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    static $position = 0;
    $position++;

    return strtr($this->getRowFormat(), array(
      '%position%'      => 'data-position="'.$position.'"',
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }
}