<?php

namespace Session\AbTest;

class AbTest {

    /* хелперы для определения варианта АБ-теста у пользователя */
    use ABHelperTrait;

    /** @var Test[] */
    private $tests = [];

    /** @var array */
    private $cookie;

    public function __construct() {
        $config = \App::config();

        $cookie = trim(\App::request()->cookies->get($config->abTest['cookieName']));
        if (0 === strpos($cookie, '{')) {
            $this->cookie = json_decode($cookie, true);
            if (null === $this->cookie) {
                $this->cookie = [];
            }
        }
        else $this->cookie = [];

        if (isset($config->abTest['tests'])) {
            foreach ($config->abTest['tests'] as $testData) {
                $test = new Test($testData);
                $test->chooseCase(isset($this->cookie[$test->getKey()]) && is_string($this->cookie[$test->getKey()]) ? $this->cookie[$test->getKey()] : null);

                if (!$test->getChosenCase()->getTraffic()) {
                    $test->chooseCase();
                }

                if ($test->isActive()) {
                    $this->cookie[$test->getKey()] = $test->getChosenCase()->getKey();
                }
                else {
                    unset($this->cookie[$test->getKey()]);
                }

                $this->tests[$test->getKey()] = $test;
            }
        }

        $testKeys = array_keys($this->tests);
        // Удаление старых тестов (которые есть в cookie, но которых нет в config'е)
        foreach ($this->cookie as $testKey => $caseKey) {
            if (!in_array($testKey, $testKeys, true)) {
                unset($this->cookie[$testKey]);
            }
        }

        ksort($this->cookie);
    }

    /**
     * @param \Http\Response|null $response
     */
    public function setCookie($response) {
        if (null === $response || !$response instanceof \Http\Response) {
            return;
        }

        $config = \App::config();
        $encodedCookie = json_encode($this->cookie);
        if (\App::request()->cookies->get($config->abTest['cookieName']) !== $encodedCookie) {
            $response->headers->setCookie(new \Http\Cookie(
                $config->abTest['cookieName'],
                $encodedCookie,
                time() + 20 * 365 * 24 * 60 * 60,
                '/',
                $config->session['cookie_domain'],
                false,
                false
            ));
        }
    }


    /** Возвращает AB-тест
     * @param $key
     * @return Test
     */
    public function getTest($key) {
        return
            isset($this->tests[$key])
            ? $this->tests[$key]
            : new Test([
                'token' => $key,
                'cases' => [],
            ]);
    }

    /** Возвращает все тесты
     * @return Test[]
     */
    public function getTests() {
        return $this->tests;
    }
}
