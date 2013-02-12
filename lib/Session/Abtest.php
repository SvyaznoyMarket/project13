<?php

namespace Session;

class Abtest {

    /** @var array */
    private $config = [];

    /** @var \Model\Abtest\Entity[] */
    private $option;

    /** @var \Model\Abtest\Entity */
    private $case;

    public function __construct(array $config) {
        $this->config = $config;

        if (!is_array($config['test']) || !count($config['test'])) {
            throw new \Exception('There must be at least one test');
        }

        foreach ($config['test'] as $option) {
            $this->option[$option['key']] = new \Model\Abtest\Entity($option);
        }

        $this->option['default'] = new \Model\Abtest\Entity([
            'traffic'  => '*',
            'key'      => 'default',
            'name'     => 'пусто',
            'ga_event' => 'default',
        ]);

        $this->case = $this->getCase();
        if (!(bool)$this->case) {
            $this->setCase();
        }
   }

    private function setCase()
    {
        $luck = mt_rand(0, 99);

        $total = 0;

        foreach ($this->option as $test) {
            if ($total >= 100) continue;

            $diff = ($test->getTraffic() !== '*') ? (int)$test->getTraffic() : (100 - $total);
            if ($luck < $total + $diff) {
                $this->case = $test;
                //\App::response()->cookies->set($this->config['cookieName'], $test['key']);

                //->headers->setCookie($cookie);

                break;
            }
            $total += $diff;
        }
    }

    /**
     * @return \Model\Abtest\Entity|null
     */
    public function getCase()
    {
        if ((bool)$this->case) {
            return $this->case;
        }
        if (\App::request()->cookies->has($this->config['cookieName'])) {
            $case = \App::request()->cookies->get($this->config['cookieName']);
            if ($this->isValid($case)) {
                return $this->option[$case];
           }
        }
        return null;
    }

    /**
     * @param string $case
     * @return bool
     */
    public function isValid($case) {
        foreach ($this->option as $test) {
            if ($test->getKey() == $case) return true;
        }

        return false;
    }
}