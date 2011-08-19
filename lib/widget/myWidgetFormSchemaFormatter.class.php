<?php

class myWidgetFormSchemaFormatter extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat                 = "<li %data_field% class=\"form-row\">\n %label%\n %error%\n  %field%%help%\n%hidden_fields%</li>\n",
    $helpFormat                = "<br /><span class=\"help\">%help%</span>",
    $errorListFormatInARow     = "  <ul class=\"error_list\">\n%errors%  </ul>\n",
    $errorRowFormat            = "<li>\n%errors%</li>\n",
    $namedErrorRowFormatInARow = "    <li>%name%: %error%</li>\n",
    $decoratorFormat           = "<ul class=\"form-content\">\n  %content%</ul>",

    $requiredTemplate          = '&nbsp;<pow class="required">*</pow>',
    $validatorSchema           = null
  ;

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null, array $params = array())
  {
    $params = myToolkit::arrayDeepMerge(array(
      'name' => null,
    ), $params);

    return strtr($this->getRowFormat(), array(
      '%data_field%'    => $params['name'] ? 'data-field="'.$params['name'].'"' : '',
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }

	public function setValidatorSchema(sfValidatorSchema $validatorSchema)
	{
		$this->validatorSchema = $validatorSchema;
	}

	public function generateLabelName($name)
	{
		$label = parent::generateLabelName($name);

    if ($this->validatorSchema instanceof sfValidatorSchema)
    {
      $fields = $this->validatorSchema->getFields();
      if (null != $fields[$name])
      {
        $field = $fields[$name];
        if ($field->hasOption('required') && $field->getOption('required'))
        {
          $label .= $this->requiredTemplate;
        }
      }
    }

		return $label;
	}
}