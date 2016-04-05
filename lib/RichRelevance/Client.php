<?php

namespace RichRelevance;

use Curl\TimeoutException;
use Http\Cookie;
use Http\Response;
use Model\Product\RichRelevanceProduct;
use Model\RichRelevance\RichRecommendation;

class Client implements \Core\ClientInterface {

    const NAME = 'RichRelevanceClient';

    /** @var string RCS-параметр из ответа */
    private static $rcs;

    /** @var array */
    private $requestConfig = [

        'excludeHtml'               => true, // If set to true, omits the HTML returned in the Relevance server response
        'returnMinimalRecItemData'  => true, // If set to true, reduces the information about the items down to external ID and click URL
    ];

    private $config = [];

    /** @var \Curl\Client */
    private $curl;

    /**
     * @param array $config
     * @param \Curl\Client $curl
     */
    public function __construct(array $config, \Curl\Client $curl) {

        $this->config = array_merge($this->config, $config);

        $this->requestConfig = array_merge($this->requestConfig, [
            'apiKey'        => \App::config()->richRelevance['apiKey'],
            'apiClientKey'  => \App::config()->richRelevance['apiClientKey'],
        ]);

        $this->curl = $curl;
    }

    public function __clone() {
        $this->curl = clone $this->curl;
    }

    /**
     * @return \Curl\Client
     */
    public function getCurl() {
        return $this->curl;
    }

    /**
     * @param string     $action
     * @param array      $params
     * @param array      $data
     * @param float|null $timeout
     * @return RichRecommendation[]
     */
    public function query($action, array $params = [], array $data = [], $timeout = null) {

        $response = [];
        $result = [];

        if (!\App::config()->richRelevance['enabled']) {
            return [];
        }

        \Debug\Timer::start(self::NAME);

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        try {
            $response = $this->curl->query($this->getUrl($action, $params), $data, $timeout);
        } catch (TimeoutException $e) {
            // Не обращаем внимания на timeout exception
            \App::exception()->remove($e);
        }

        \Debug\Timer::stop(self::NAME);

        if (isset($response['placements']) && is_array($response['placements'])) {
            foreach ($response['placements'] as $placement) {
                $result[$placement['placement']] = new RichRecommendation($placement);
            }
        }

        if (isset($response['rcs'])) {
            self::$rcs = $response['rcs'];
        }

        return $result;
    }

    /**
     * @param string        $action
     * @param array         $params
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($action, array $params = [], array $data = [], $successCallback = null, $failCallback = null, $timeout = null) {

        if (!\App::config()->richRelevance['enabled']) {
            return false;
        }

        \Debug\Timer::start(self::NAME);

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }


        $result = $this->curl->addQuery($this->getUrl($action, $params), $data, $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop(self::NAME);

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start(self::NAME);

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop(self::NAME);
    }

    /**
     * @param string $method
     * @param array  $params
     *
     * @return string
     */
    private function getUrl($method, array $params = []) {

        $params = array_merge(
            $params,
            $this->requestConfig,
            [
                'sessionId' => \App::session()->getId(),
                'rid'       => \App::user()->getRegionId()
            ]
        );

        if (\App::user()->getEntity()) {
            $params['userId'] = \App::user()->getEntity()->getId();
        }

        if (\App::request()->cookies->has(\App::config()->richRelevance['rcs_cookie'])) {
            $params['rcs'] = \App::request()->cookies->get(\App::config()->richRelevance['rcs_cookie']);
        }

        $url = sprintf(
            '%s%s?%s',
            $this->config['apiUrl'],
            $method,
            http_build_query($params)
        );

        return $url;
    }

    public function setCookie(Response $response = null) {
        if ($response && self::$rcs) {
            $cookie = new Cookie(
                \App::config()->richRelevance['rcs_cookie'],
                self::$rcs,
                time() + 365 * 24 * 60 * 60,
                '/',
                \App::config()->session['cookie_domain'],
                false,
                true
            );
            $response->headers->setCookie($cookie);
        }
    }
}