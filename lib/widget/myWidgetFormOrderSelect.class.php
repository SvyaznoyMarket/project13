<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSelect represents a select HTML tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSelect.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class myWidgetFormOrderSelect extends sfWidgetFormChoiceBase
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * choices:  An array of possible choices (required)
   *  * multiple: true if the select tag must allow multiple selections
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormChoiceBase
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('multiple', false);
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if ($this->getOption('multiple'))
    {
      $attributes['multiple'] = 'multiple';

      if ('[]' != substr($name, -2))
      {
        $name .= '[]';
      }
    }

    $choices = $this->getChoices();

    return $this->renderContentTag('select', "\n".implode("\n", $this->getOptionsForSelect($value, $choices))."\n", array_merge(array('name' => $name), $attributes));
  }

  /**
   * Returns an array of option tags for the given choices
   *
   * @param  string $value    The selected value
   * @param  array  $choices  An array of choices
   *
   * @return array  An array of option tags
   */
  protected function getOptionsForSelect($value, $choices)
  {
    $mainAttributes = $this->attributes;
    $this->attributes = array();

    if (!is_array($value))
    {
      $value = array($value);
    }

    $value = array_map('strval', array_values($value));
    $value_set = array_flip($value);

    $options = array();
    foreach ($choices as $key => $option)
    {
      $is_data = false;
      if (is_array($option))
      {
        //Ищу есть ли data- значения в опциях
        $option_keys = array_keys($option);

        foreach ($option_keys as $option_key)
        {
          $is_data |= false !== strstr($option_key, 'data-');
        }
      }
      if (is_array($option) && !$is_data)
      {
        $options[] = $this->renderContentTag('optgroup', implode("\n", $this->getOptionsForSelect($value, $option)), array('label' => self::escapeOnce($key)));
      }
      elseif (is_array($option) && $is_data && isset($option['name']))
      {
        $attributes = array('value' => self::escapeOnce($key));
        if (isset($value_set[strval($key)]))
        {
          $attributes['selected'] = 'selected';
        }
        foreach ($option as $option_key => $option_value)
        {
          if (strstr($option_key, 'data-'))
          {
            $attributes[$option_key] = $option_value;
          }
        }

        $options[] = $this->renderContentTag('option', self::escapeOnce($option['name']), $attributes);
      }
      else
      {
        $attributes = array('value' => self::escapeOnce($key));
        if (isset($value_set[strval($key)]))
        {
          $attributes['selected'] = 'selected';
        }

        $options[] = $this->renderContentTag('option', self::escapeOnce($option), $attributes);
      }
    }

    $this->attributes = $mainAttributes;

    return $options;
  }
}
