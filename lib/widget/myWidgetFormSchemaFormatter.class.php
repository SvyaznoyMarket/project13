<?php

class myWidgetFormSchemaFormatter extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat                 = "<li %data_field% class=\"form-row\">\n %label%\n %error%\n  <div class=\"content\">%field%</div>%help%\n%hidden_fields%</li>\n",
    $helpFormat                = "<br /><span class=\"help\">%help%</span>",
    $errorListFormatInARow     = "  <ul class=\"error_list\">\n%errors%  </ul>\n",
    $errorRowFormat            = "<li>\n%errors%</li>\n",
    $namedErrorRowFormatInARow = "    <li>%name%: %error%</li>\n",
    $decoratorFormat           = "<ul class=\"form-content\">\n  %content%</ul>",

    $requiredFormat            = ' <pow class="required">*</pow>',
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
		return parent::generateLabelName($name).$this->getRequiredMark($name);
	}

  public function generateLabel($name, $attributes = array())
  {
    $labelName = $this->generateLabelName($name);

    if (false === $labelName)
    {
      return '';
    }

    if (!isset($attributes['for']))
    {
      $attributes['for'] = $this->widgetSchema->generateId($this->widgetSchema->generateName($name));
    }

    return $this->widgetSchema->renderContentTag('label', $labelName, $attributes);
  }

  protected function getRequiredMark($name)
  {
    $mark = '';

    if ($this->validatorSchema instanceof sfValidatorSchema)
    {
      $fields = $this->validatorSchema->getFields();
      if (null != $fields[$name])
      {
        $field = $fields[$name];
        if ($field->hasOption('required') && $field->getOption('required'))
        {
          $mark = $this->requiredFormat;
        }
      }
    }

    return $mark;
  }
}