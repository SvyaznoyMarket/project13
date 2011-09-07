<?php

/**
 * Base project form.
 *
 * @package    enter
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class BaseForm extends sfFormSymfony
{
  public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
  {
    $this->setDefaults($defaults);
    $this->options = $options;
    $this->localCSRFSecret = $CSRFSecret;

    $this->validatorSchema = new myValidatorSchema();
    $this->widgetSchema    = new myWidgetFormSchema();
    $this->errorSchema     = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setup();
    $this->configure();

    $this->addCSRFProtection($this->localCSRFSecret);
    $this->resetFormFields();

    if (self::$dispatcher)
    {
      self::$dispatcher->notify(new sfEvent($this, 'form.post_configure'));
    }
  }

  public function configure()
  {
    $this->disableCSRFProtection();

    $this->widgetSchema->setFormFormatterName('default');
  }

  public function embedForm($name, sfForm $form, $decorator = null)
  {
    $name = (string) $name;
    if (true === $this->isBound() || true === $form->isBound())
    {
      throw new LogicException('A bound form cannot be embedded');
    }

    $this->embeddedForms[$name] = $form;

    $form = clone $form;
    unset($form[self::$CSRFFieldName]);

    $widgetSchema = $form->getWidgetSchema();

    $this->setDefault($name, $form->getDefaults());

    $decorator = null === $decorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $decorator;

    $this->widgetSchema[$name] = new myWidgetFormSchemaDecorator($widgetSchema, $decorator);
    $this->validatorSchema[$name] = $form->getValidatorSchema();

    $this->resetFormFields();
  }

  public function embedFormForEach($name, sfForm $form, $n, $decorator = null, $innerDecorator = null, $options = array(), $attributes = array(), $labels = array())
  {
    if (true === $this->isBound() || true === $form->isBound())
    {
      throw new LogicException('A bound form cannot be embedded');
    }

    $this->embeddedForms[$name] = new sfForm();

    $form = clone $form;
    unset($form[self::$CSRFFieldName]);

    $widgetSchema = $form->getWidgetSchema();

    // generate default values
    $defaults = array();
    for ($i = 0; $i < $n; $i++)
    {
      $defaults[$i] = $form->getDefaults();

      $this->embeddedForms[$name]->embedForm($i, $form);
    }

    $this->setDefault($name, $defaults);

    $decorator = null === $decorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $decorator;
    $innerDecorator = null === $innerDecorator ? $widgetSchema->getFormFormatter()->getDecoratorFormat() : $innerDecorator;

    $this->widgetSchema[$name] = new myWidgetFormSchemaDecorator(new myWidgetFormSchemaForEach(new myWidgetFormSchemaDecorator($widgetSchema, $innerDecorator), $n, $options, $attributes), $decorator);
    $this->validatorSchema[$name] = new sfValidatorSchemaForEach($form->getValidatorSchema(), $n);

    // generate labels
    for ($i = 0; $i < $n; $i++)
    {
      if (!isset($labels[$i]))
      {
        $labels[$i] = sprintf('%s (%s)', $this->widgetSchema->getFormFormatter()->generateLabelName($name), $i);
      }
    }

    $this->widgetSchema[$name]->setLabels($labels);

    $this->resetFormFields();
  }

  public function setWidgets(array $widgets)
  {
    $this->setWidgetSchema(new myWidgetFormSchema($widgets));

    return $this;
  }

  public function render($attributes = array())
	{
		if ((null != $formatter = $this->widgetSchema->getFormFormatter()) && $this->getOption('mark_required', true))
    {
			$formatter->setValidatorSchema($this->getValidatorSchema());
		}

		return parent::render($attributes);
	}

  public function getFormFieldSchema()
  {
    if (null === $this->formFieldSchema)
    {
      $values = $this->isBound ? $this->taintedValues : $this->defaults + $this->widgetSchema->getDefaults();

      $this->formFieldSchema = new myFormFieldSchema($this->widgetSchema, null, null, $values, $this->errorSchema);
    }

    return $this->formFieldSchema;
  }

  public function offsetGet($name)
  {
    if (!isset($this->formFields[$name]))
    {
      if (!$widget = $this->widgetSchema[$name])
      {
        throw new InvalidArgumentException(sprintf('Widget "%s" does not exist.', $name));
      }

      if ($this->isBound)
      {
        $value = isset($this->taintedValues[$name]) ? $this->taintedValues[$name] : null;
      }
      else if (isset($this->defaults[$name]))
      {
        $value = $this->defaults[$name];
      }
      else
      {
        $value = $widget instanceof sfWidgetFormSchema ? $widget->getDefaults() : $widget->getDefault();
      }

      $class = $widget instanceof sfWidgetFormSchema ? 'myFormFieldSchema' : 'myFormField';

      $this->formFields[$name] = new $class($widget, $this->getFormFieldSchema(), $name, $value, $this->errorSchema[$name]);
    }

    return $this->formFields[$name];
  }
}
