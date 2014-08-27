<?php

namespace Session;

class AbtestJson {

    /** @var array */
    protected $config = [];

    /** @var \Model\AbtestJson\Entity[] */
    protected $option = [];

    /** @var \Model\AbtestJson\Entity */
    protected $case;

    /** @var array */
    protected $values = [];

    /** @var array */
    protected $catalogJson = [];

    public function __construct($catalogJson) {
        // в случае конфликта имен куки с Abtest переименовываем куку
        if(!empty($catalogJson['abtest']['cookieName']) && $catalogJson['abtest']['cookieName'] == \App::config()->abTest['cookieName']) {
            $catalogJson['abtest']['cookieName'] .= '_json';
        }

        $this->config = array_merge([
            'cookieName' => 'switch_json',
            'bestBefore' => "2000-01-01",
            'enabled'    => false,
            'test'       => [],
        ], empty($catalogJson['abtest']) ? [] : $catalogJson['abtest']);

        $this->values = empty($catalogJson['abtest_values']) ? [] : $catalogJson['abtest_values'];
        $this->catalogJson = $catalogJson;

        if (isset($this->config['test']) && is_array($this->config['test'])) {
            foreach ($this->config['test'] as $option) {
                $this->option[$option['key']] = new \Model\AbtestJson\Entity($option);
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

    protected function setCase() {
        $luck = mt_rand(0, 99);
        $total = 0;

        foreach ($this->option as $test) {
            if ($total >= 100) continue;

            $diff = ($test->getTraffic() !== '*') ? (int)$test->getTraffic() : (100 - $total);
            if ($luck < $total + $diff) {
                $this->case = $test;
                break;
            }
            $total += $diff;
        }
    }

    /**
     * @return \Model\AbtestJson\Entity|null
     */
    public function getCase() {
        if ((bool)$this->case) {
            return $this->case;
        }

        if (!$this->isActive()) {
            return $this->option['default'];
        }
        if (\App::request()->cookies->has($this->config['cookieName'])) {
            $case = \App::request()->cookies->get($this->config['cookieName']);
            if ($this->isValid($case)) {
                return $this->option[$case];
            }
        }
        return null;
    }

    public function setCookie($response = null) {
        if (null === $response || !$response instanceof \Http\Response ) {
            return;
        }

        /* @var $response \Http\Response */

        if (!$this->isActive())
        {
            $cookie = new \Http\Cookie(
                $this->config['cookieName'],
                'default',
                time() + 10,
                '/',
                \App::config()->session['cookie_domain'],
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
        } else {
            $case = $this->getCase();

            $cookie = new \Http\Cookie(
                $this->config['cookieName'],
                $case->getKey(),
                strtotime($this->config['bestBefore']),
                '/',
                \App::config()->session['cookie_domain'],
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
        }

        if (!\App::request()->cookies->has($this->config['cookieName'])) {
            $response->headers->setCookie($cookie);
        }
    }

    /**
     * @return array
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * @return array|\Model\AbtestJson\Entity[]
     */
    public function getOption() {
        return $this->option;
    }

    /**
     * @param string $case
     * @return bool
     */
    protected function isValid($case) {
        foreach ($this->option as $test) {
            if ($test->getKey() == $case) return true;
        }

        return false;
    }

    /**
     * Возвращает опцию по умолчанию
     *
     * @return \Model\AbtestJson\Entity
     */
    protected function getDefaultOption() {
        return new \Model\AbtestJson\Entity([
            'traffic'  => '*',
            'key'      => 'default',
            'name'     => 'пусто',
            'ga_event' => 'default',
        ]);
    }

    /**
     * @return bool
     */
    public function isActive() {
        return (bool)$this->config['enabled'] &&
        (strtotime($this->config['bestBefore']) > strtotime('now'));
    }
}
