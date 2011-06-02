<?php

class sfWidgetFormSchemaFormatterDefault extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<li class=\"form-row\">\n <span class=\"form-row-label\">%label%</span>\n %error%\n  %field%%help%\n%hidden_fields%</li>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = "<br /><span class=\"help\">%help%</span>",
    $decoratorFormat = "<ul class=\"form-content\">\n  %content%</ul>";
}