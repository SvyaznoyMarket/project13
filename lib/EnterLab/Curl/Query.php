<?php

namespace EnterLab\Curl;

class Query
{
    /** @var Request */
    public $request;
    /** @var Response */
    public $response;
    /** @var callable */
    public $resolveCallback;
    /** @var callable */
    public $rejectCallback;

    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
    }

    public function __clone()
    {
        $this->request = clone $this->request;
        $this->response = clone $this->response;
    }
}