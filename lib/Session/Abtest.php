<?php

namespace Session;

class Abtest {

    /** @var array */
    protected $config = [];

    /** @var \Model\Abtest\Entity[] */
    protected $option = [];

    /** @var \Model\Abtest\Entity */
    protected $case;

    public function __construct(array $config) {
        $this->config = $config;

        if (isset($config['test']) && is_array($config['test'])) {
            foreach ($config['test'] as $option) {
                $this->option[$option['key']] = new \Model\Abtest\Entity($option);
            }
        }

        $this->option['default'] = $this->getDefaultOption();

        $this->case = $this->getCase();
        if (!(bool)$this->case) {
            $this->setCase();
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
     * @return \Model\Abtest\Entity|null
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
                time() + 10,//\App::config()->abtest['checkPeriod'],
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
     * @return array|\Model\Abtest\Entity[]
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
     * @return \Model\Abtest\Entity
     */
    protected function getDefaultOption() {
        return new \Model\Abtest\Entity([
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
