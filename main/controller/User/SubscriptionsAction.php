<?php


namespace Controller\User;


class SubscriptionsAction {

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {

        if ($request->isMethod('post')) {
            try {
                $this->setData($request);
            } catch (\Exception $e) {

            }
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData()
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\SubscriptionsPage();
        $page->setParam('userChannels', $data['userChannels']);

        return new \Http\Response($page->show());

    }

    /** Возвращает массив заказов и продуктов
     * @return array
     */
    private function getData() {

        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $userChannels = [];
        $channelCollection = [];

        $client->addQuery('subscribe/get',['token'=>$user->getToken()],[], function ($data) use (&$userChannels) {
            foreach ($data as $channel) {
                $userChannels[] = new \Model\User\SubscriptionEntity($channel);
            }
        });

        $client->addQuery('subscribe/get-channel', [], [], function ($data) use (&$channelCollection) {
            foreach ($data as $channel) {
                $channelCollection[] = new \Model\Subscribe\Channel\Entity($channel);
            }
        });

        $client->execute();

        foreach ($userChannels as &$channel) {
            /** @var $channel \Model\User\SubscriptionEntity */
            if ($channel->getChannelId()) {
                $channels = array_filter($channelCollection, function ($item) use ($channel) {
                    /** @var $item \Model\Subscribe\Channel\Entity */
                    return $channel->getChannelId() == $item->getId();
                });
                $channel->setChannel(reset($channels));
            }
        }

        return [ 'userChannels' => $userChannels ];

    }

    /**
     * @param \Http\Request $request
     */
    private function setData(\Http\Request $request) {



    }

} 