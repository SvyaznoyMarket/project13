<?php

namespace Model\EnterprizeCoupon;

class Repository {
    /** @var \Scms\Client */
    private $client;
    private $entityClass = '\Model\EnterprizeCoupon\Entity';

    /**
     * @param \Scms\Client $client
     * @param \Core\ClientInterface $coreClient
     */
    public function __construct(\Scms\Client $client) {
        $this->client = $client;
    }

    /**
     * @param $done
     * @param null $fail
     */
    public function prepareCollection($done, $member_type = null, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $params = [];
        if (!is_null($member_type)) {
            $params['member_type'] = $member_type;
        }  elseif (\App::user()->getEntity() && \App::user()->getEntity()->isEnterprizeMember()){
            $params['member_type'] = 1;
        }

        $this->client->addQuery('coupon/get', $params, [], $done, $fail);
    }

    /**
     * @param int $member_type
     * @param $done
     * @param null $fail
     */
    public function prepareCollectionByMemberType($member_type = 0, $done, $fail = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $this->client->addQuery('coupon/get', ['member_type' => $member_type], [], $done, $fail);
    }

    /**
     * @param null $uid
     * @return null
     */
    public function getEntityByToken($uid = null) {
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

        $entityClass = $this->entityClass;

        $coupon = null;
        try {
            if (empty($uid)) {
                throw new \Exception('uid не передан');
            }

            // TODO SITE-4133 Убрать проверку, когда в логах появится инфа с тегом 'v2/coupon/get?uid=null'
            if ('null' === $uid) {
                \App::logger()->error(\App::request()->server->all(), ['v2/coupon/get?uid=null']);
                throw new \Exception('uid не передан');
            }

            $result = $this->client->query('coupon/get', ['uid' => $uid]);

            if (!(bool)$result || !is_array($result)) {
                throw new \Exception(sprintf('Купон uid=%s не получен', $uid));
            }

            $data = reset($result);
            $coupon = new $entityClass($data);

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
        //\App::logger()->debug('Exec ' . __METHOD__ . ' ' . json_encode(func_get_args(), JSON_UNESCAPED_UNICODE));

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