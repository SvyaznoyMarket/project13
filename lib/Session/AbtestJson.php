<?php

namespace Session;

class AbtestJson extends Abtest {

    /** @var array */
    protected $values = [];

    /** @var array */
    protected $catalogJson = [];

    public function __construct($catalogJson) {
        // в случае конфликта имен куки с Abtest переименовываем куку
        if(!empty($catalogJson['abtest']['cookieName']) && $catalogJson['abtest']['cookieName'] == \App::config()->abtest['cookieName']) {
            $catalogJson['abtest']['cookieName'] .= '_json';
        }

        $this->config = empty($catalogJson['abtest']) ? [] : $catalogJson['abtest'];
        $this->values = empty($catalogJson['abtest_values']) ? [] : $catalogJson['abtest_values'];
        $this->catalogJson = $catalogJson;

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
    public function getTestCatalogJson() {
        $key = $this->getCase()->getKey();

        if($this->hasEnoughData()) {
            return $this->values[$key];
        }

        return $this->catalogJson;
    }

    /**
     * @return array
     */
    public function hasEnoughData() {
        $key = $this->getCase()->getKey();

        if($key == 'default' || !empty($this->values) && is_array($this->values) && array_key_exists($key, $this->values)) {
            return true;
        } else {
            return false;
        }
    }

}
