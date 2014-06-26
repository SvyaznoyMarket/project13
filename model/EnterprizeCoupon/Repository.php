<?php

namespace Model\EnterprizeCoupon;

class Repository {
    /** @var \Scms\ClientV2 */
    private $client;
    private $entityClass = '\Model\EnterprizeCoupon\Entity';

    /**
     * @param \Scms\ClientV2 $client
     * @param \Core\ClientInterface $coreClient
     */
    public function __construct(\Scms\ClientV2 $client) {
        $this->client = $client;
    }

    /**
     * @param $done
     * @param null $fail
     */
    public function prepareCollection($done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('coupon/get', [], [], $done, $fail);
    }

    /**
     * @param int $member_type
     * @param $done
     * @param null $fail
     */
    public function prepareCollectionByMemberType($member_type = 0, $done, $fail = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('coupon/get', ['member_type' => $member_type], [], $done, $fail);
    }

    /**
     * @param $token
     * @return Entity|null
     */
    public function getEntityByToken($token) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

//        /** @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity|null */
//        $enterpizeCoupon = null;
//        \App::dataStoreClient()->addQuery('enterprize/coupon-type.json', [], function($data) use (&$enterpizeCoupon, $enterprizeToken) {
//            foreach ((array)$data as $item) {
//                if ($enterprizeToken == $item['token']) {
//                    $enterpizeCoupon = new \Model\EnterprizeCoupon\Entity($item);
//                }
//            }
//        });
//        \App::dataStoreClient()->execute();

        $entityClass = $this->entityClass;

        $coupon = null;
        try {
            $result = $this->client->query('coupon/get');

            if (!(bool)$result || !is_array($result)) {
                throw new \Exception('Купоны не получены');
            }

            foreach ((array)$result as $item) {
                $entity = new $entityClass($item);
                if ($token == $entity->getToken()) {
                    $coupon = $entity;
                }
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        return $coupon;
    }

    /**
     * @param null $keyword
     * @return null
     */
    public function getEntityFromPartner($keyword = null) {
        \App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $entityClass = $this->entityClass;
        $coupon = null;

        // запрашиваем фишку партнера
        try {
            $keyword = trim((string)$keyword);
            if (!(bool)$keyword) {
                throw new \Exception('Поле keyword не заполнено');
            }

            $result = $this->client->query('coupon/partner', ['keyword' => $keyword]);

            if (!(bool)$result || !is_array($result)) {
                throw new \Exception(sprintf('Купон от партнера не получен для keyword=%s', $keyword));
            }

            $coupon = new $entityClass($result);

        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        return $coupon;
    }
}