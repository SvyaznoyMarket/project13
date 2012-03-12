<?php

class myWidgetFormComponent extends sfWidgetForm
{
    protected function configure($options = array(), $attributes = array())
    {
        $this->addRequiredOption('component');
        $this->addOption('component_param', array());
    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $component = $this->getOption('component');
        $param = myToolkit::arrayDeepMerge(array('name' => $name, 'value' => $value, 'attributes' => $attributes, 'errors' => $errors), $this->getOption('component_param'));

        if (is_array($component) && (2 == count($component)))
        {
            return get_component($component[0], $component[1], $param);
        }
        else if (false !== strpos($component, '/'))
        {
            return get_partial($component, $param);
        }

        throw new Exception('Invalid component or partial name');
    }
}