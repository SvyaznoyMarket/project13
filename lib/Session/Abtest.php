<?php

namespace Session;

class Abtest {

    /** @var array */
    private $config = [];

    /** @var \Model\Abtest\Entity[] */
    private $option = [];

    /** @var \Model\Abtest\Entity */
    private $case;

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

    private function setCase() {
        $luck = mt_rand(0, 99);
        $total = 0;
// file_put_contents('/tmp/logger.txt', json_encode($this->getOption()).PHP_EOL, FILE_APPEND);

        foreach ($this->option as $test) {
            if ($total >= 100) continue;

            $diff = ($test->getTraffic() !== '*') ? (int)$test->getTraffic() : (100 - $total);
            if ($luck < $total + $diff) {
                $this->case = $test;
                break;
            }
            $total += $diff;
        }
// file_put_contents('/tmp/logger.txt', $this->case->getKey().PHP_EOL, FILE_APPEND);
// file_put_contents('/tmp/logger.txt', '======================================'.PHP_EOL, FILE_APPEND);
    }

    /**
     * @return \Model\Abtest\Entity|null
     */
    public function getCase($z = false) {
// if($z) file_put_contents('/tmp/logger.txt', '1111111'.PHP_EOL, FILE_APPEND);
        if ((bool)$this->case) {
// if($z) file_put_contents('/tmp/logger.txt', '222222'.PHP_EOL, FILE_APPEND);
            return $this->case;
        }
// if($z) file_put_contents('/tmp/logger.txt', '333333'.PHP_EOL, FILE_APPEND);

        if (strtotime($this->config['bestBefore']) <= strtotime('now') || !(bool)$this->config['enabled']) {
// if($z) file_put_contents('/tmp/logger.txt', '444444'.PHP_EOL, FILE_APPEND);
            return $this->option['default'];
        }
// if($z) file_put_contents('/tmp/logger.txt', '55555555'.PHP_EOL, FILE_APPEND);
        if (\App::request()->cookies->has($this->config['cookieName'])) {
// if($z) file_put_contents('/tmp/logger.txt', '6666666'.PHP_EOL, FILE_APPEND);
            $case = \App::request()->cookies->get($this->config['cookieName']);
            if ($this->isValid($case)) {
// if($z) file_put_contents('/tmp/logger.txt', '77777777'.PHP_EOL, FILE_APPEND);
                return $this->option[$case];
           }
        }
// if($z) file_put_contents('/tmp/logger.txt', '8888888888'.PHP_EOL, FILE_APPEND);
        return null;
    }

    public function setCookie($response = null) {
// file_put_contents('/tmp/logger.txt', json_encode(\App::abTest()->getOption()).PHP_EOL, FILE_APPEND);

        if (null === $response || !$response instanceof \Http\Response ) {
            return;
        }

        /* @var $response \Http\Response */

        if (strtotime($this->config['bestBefore']) <= strtotime('now') || !(bool)$this->config['enabled'])
        {
            $cookie = new \Http\Cookie(
                $this->config['cookieName'],
                'default',
                0,
                '/',
                null,
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
        } else {
            $case = $this->getCase(true);

// foreach (get_class_methods($case) as $key => $method) {
//     if(preg_match('/^get.*/', $method)) {
//         try {
//             file_put_contents('/tmp/logger.txt', "$method: ".json_encode($case->$method()).PHP_EOL, FILE_APPEND);
//         } catch (\Exception $e) {
//         }
//     }
// }

            $cookie = new \Http\Cookie(
                $this->config['cookieName'],
                $case->getKey(),
                strtotime($this->config['bestBefore']),
                '/',
                null,
                false,
                false // важно httpOnly=false, чтобы js мог получить куку
            );
        }

        if (!\App::request()->cookies->has($this->config['cookieName'])) {
            $response->headers->setCookie($cookie);
        }
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
    private function isValid($case) {
        foreach ($this->option as $test) {
            if ($test->getKey() == $case) return true;
        }

        return false;
    }

    /**
     * Определяет нужно ли переопределять конфигурацию для абтеста и достаточно ли для этого данных
     *
     * @param array $catalogJson
     * @return bool
     */
    public function shouldOverrideByJson($catalogJson) {
        $missingKey = false;
        if(!empty($catalogJson['abtest']['test']) && is_array($catalogJson['abtest']['test'])) {
            foreach ($catalogJson['abtest']['test'] as $test) {
                if(empty($test['key']) || !in_array($test['key'], array_keys($catalogJson['abtest']))) {
                    $missingKey = true;
                    break;
                }
            }
        }

        return !empty($catalogJson['abtest']['cookieName']) &&
               !empty($catalogJson['abtest']['bestBefore']) &&
               !empty($catalogJson['abtest']['enabled']) &&
               !in_array($catalogJson['abtest']['enabled'], ['false']) &&
               !empty($catalogJson['abtest']['test']) &&
               !$missingKey;
    }

    /**
     * Возвращает опцию по умолчанию
     *
     * @return \Model\Abtest\Entity
     */
    private function getDefaultOption() {
        return new \Model\Abtest\Entity([
            'traffic'  => '*',
            'key'      => 'default',
            'name'     => 'пусто',
            'ga_event' => 'default',
        ]);
    }

    /**
     * Переопределяет абтест
     *
     * @param array $config
     */
    private function reinitialize($config) {
        $this->config = $config;

        $this->option = [];

        if (isset($config['test']) && is_array($config['test'])) {
            foreach ($config['test'] as $option) {
                $this->option[$option['key']] = new \Model\Abtest\Entity($option);
            }
        }

        $this->option['default'] = $this->getDefaultOption();

        $this->case = $this->getCase();
        if (!(bool)$this->case || !in_array($this->case->getKey(), array_keys($this->option))) {
            $this->setCase();
        }
    }

    /**
     * Переопределяет конфигурацию для абтеста
     *
     * @param array $catalogJson
     */
    public function overrideByJson($catalogJson) {
        \App::config()->abtest['cookieName'] = $catalogJson['abtest']['cookieName'];
        \App::config()->abtest['enabled']    = $catalogJson['abtest']['enabled'];
        \App::config()->abtest['bestBefore'] = $catalogJson['abtest']['bestBefore'];
        \App::config()->abtest['test']       = $catalogJson['abtest']['test'];
        $this->reinitialize(\App::config()->abtest);
    }

}