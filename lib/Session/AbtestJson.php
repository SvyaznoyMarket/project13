<?php

namespace Session;

class AbtestJson extends Abtest {

    /** @var array */
    protected $values = [];

    public function __construct($catalogJson) {
        // в случае конфликта имен куки с Abtest переименовываем куку
        if(!empty($catalogJson['abtest_config']['cookieName']) && $catalogJson['abtest_config']['cookieName'] == \App::config()->abtest['cookieName']) {
            $catalogJson['abtest_config']['cookieName'] .= '_json';
        }

        $this->config = $catalogJson['abtest'];
        $this->values = $catalogJson['abtest_values'];

        if (isset($this->config['test']) && is_array($this->config['test'])) {
            foreach ($this->config['test'] as $option) {
                $this->option[$option['key']] = new \Model\Abtest\Entity($option);
            }
        }

        if(!isset($this->option['default'])) {
            $this->option['default'] = $this->getDefaultOption();
        }

        $this->case = $this->getCase();
        if (!(bool)$this->case) {
            $this->setCase();
        }
    }

    /**
     * @return array
     */
    public function getValues() {
        return $this->values;
    }

}
