<?php

namespace EnterQuery\User
{
    use EnterQuery\User\Update\Response;
    use EnterQuery\User\Update\User;

    class Update
    {
        use \EnterQuery\CurlQueryTrait;
        use \EnterQuery\CoreQueryTrait;

        /** @var string */
        public $token;
        /** @var User */
        public $user;
        /** @var Response */
        public $response;

        public function __construct($token = null, $user = null)
        {
            $this->response = new Response();

            $this->token = $token;
            $this->user = $user ?: new User();
        }

        /**
         * @return $this
         */
        public function prepare()
        {
            $data = [];
            if (null !== $value = $this->user->firstName) {
                $data['first_name'] = $value;
            }
            if (null !== $value = $this->user->middleName) {
                $data['middle_name'] = $value;
            }
            if (null !== $value = $this->user->lastName) {
                $data['last_name'] = $value;
            }
            if (null !== $value = $this->user->sex) {
                $data['sex'] = $value;
            }
            if (null !== $value = $this->user->email) {
                $data['email'] = $value;
            }
            if (null !== $value = $this->user->phone) {
                $data['mobile'] = $value;
            }
            if (null !== $value = $this->user->homePhone) {
                $data['phone'] = $value;
            }
            if (null !== $value = $this->user->birthday) {
                $data['birthday'] = $value;
            }
            if (null !== $value = $this->user->occupation) {
                $data['occupation'] = $value;
            }

            $this->prepareCurlQuery(
                $this->buildUrl(
                    'v2/user/update',
                    [
                        'token' => $this->token
                    ]
                ),
                $data, // data
                function($response, $statusCode) {
                    $result = $this->decodeResponse($response, $statusCode)['result'];

                    $this->response->confirmed = isset($result['confirmed']) ? (bool)$result['confirmed'] : null;

                    return $result; // for cache
                },
                3,
                [0]
            );

            return $this;
        }
    }
}

namespace EnterQuery\User\Update
{
    class User
    {
        /** @var string */
        public $firstName;
        /** @var string */
        public $lastName;
        /** @var string */
        public $middleName;
        /** @var string */
        public $email;
        /** @var int */
        public $sex;
        /** @var string */
        public $phone;
        /** @var string */
        public $homePhone;
        /** @var string */
        public $birthday;
        /** @var string */
        public $occupation;
    }

    class Response
    {
        /** @var bool|null */
        public $confirmed;
    }
}