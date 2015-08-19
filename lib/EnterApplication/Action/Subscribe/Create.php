<?php

namespace EnterApplication\Action\Subscribe
{
    use EnterApplication\Action\Subscribe\Create\Request;
    use EnterApplication\Action\Subscribe\Create\Response;
    use EnterQuery as Query;

    class Create {
        use \EnterApplication\CurlTrait;
        use \EnterApplication\Action\ActionTrait;

        /**
         * @param Request $request
         * @return Response
         */
        public function execute(Request $request)
        {
            // response
            $response = new Response();

            $curl = $this->getCurl();

            $createQuery = new Query\Subscribe\Create();
            $createQuery->email = $request->email;
            $createQuery->channelId = $request->channelId;
            $createQuery->userToken = $request->userToken;
            $createQuery->prepare();

            $curl->execute();

            if ($e = $createQuery->error) {
                $error = $response->errors->push();
                $error->code = $e->getCode();
                $error->message = $e->getMessage();

                switch ($e->getCode()) {
                    case 910:
                        $error->id = 'already_subscribed';
                        $error->message = 'Уже подписан';
                        break;
                    case 850:
                        $error->id = 'invalid_email';
                        $error->message = 'Неверный email';
                        break;
                    default:
                        break;
                }
            }

            return $response;
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
        }
    }
}

namespace EnterApplication\Action\Subscribe\Create
{
    use EnterQuery as Query;
    use EnterModel as Model;

    class Request
    {
        /** @var string */
        public $email;
        /** @var string|null */
        public $userToken;
        /** @var string|null */
        public $channelId;
    }

    class Response
    {
        /** @var Model\ErrorCollection */
        public $errors;

        public function __construct() {
            $this->errors = new Model\ErrorCollection();
        }
    }
}