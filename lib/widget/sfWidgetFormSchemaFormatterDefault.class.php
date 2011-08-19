<?php

class sfWidgetFormSchemaFormatterDefault extends myWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li %data_field% class=\"form-row\">\n %label%\n %error%\n  %field%%help%\n%hidden_fields%</li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = "<br /><span class=\"help\">%help%</span>",
    $decoratorFormat = "<ul class=\"form-content\">\n  %content%</ul>",

    $requiredTemplate= '&nbsp;<pow class="required">*</pow>',
    $validatorSchema = null;

}