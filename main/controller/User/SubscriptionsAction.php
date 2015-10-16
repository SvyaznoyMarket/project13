<?php


namespace Controller\User;


/** Подписки пользователя: получение и сохранение
 * Class SubscriptionsAction
 * @package Controller\User
 */
class SubscriptionsAction extends PrivateAction {

    private $client;
    private $user;
    private $session;

    public function __construct() {
        parent::__construct();
        $this->client = \App::coreClientV2();
        $this->user = \App::user();
        $this->session = \App::session();
    }

    public function execute(\Http\Request $request) {

        if ($request->isMethod('post')) {
            try {
                $this->setData($request);
                $this->session->flash(['type' => 'success', 'message' => 'Параметры подписок сохранены']);
            } catch (\Curl\Exception $e) {
                \App::logger()->error($e, ['curl', 'error']);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
                \App::exception()->remove($e);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['error']);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
            }
            return new \Http\RedirectResponse(\App::router()->generate('user.subscriptions'));
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData()
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\SubscriptionsPage();
        $page->setParam('userChannels', $data['userChannels']);
        $page->setParam('flash', $this->session->flash());

        return new \Http\Response($page->show());

    }

    /** Возвращает массив подписок пользователя
     * @return array
     */
    private function getData() {

        //\App::logger()->debug('Exec ' . __METHOD__);

        $userChannels = [];
        $channelCollection = [];

        $this->client->addQuery(
            'subscribe/get',
            ['token'=>$this->user->getToken()],
            [],
            function ($data) use (&$userChannels) {
                foreach ($data as $channel) {
                    $userChannels[] = new \Model\User\SubscriptionEntity($channel);
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        $this->client->addQuery(
            'subscribe/get-channel',
            [],
            [],
            function ($data) use (&$channelCollection) {
                foreach ($data as $channel) {
                    $channelCollection[] = new \Model\Subscribe\Channel\Entity($channel);
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        $this->client->execute();

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

    /** Сохраняет данные подписок
     * @param \Http\Request $request
     * @throws \Exception
     */
    private function setData(\Http\Request $request) {

        $formData = $request->request->all();

        foreach ($formData['channel'] as &$channelData) {
            if (isset($channelData['is_confirmed'])) $channelData['is_confirmed'] = true;
            else $channelData['is_confirmed'] = false;
        }

        $response = $this->client->query('subscribe/set', ['token'=>$this->user->getToken()], $formData['channel']);

        if (!isset($response['confirmed']) || $response['confirmed'] == false) throw new \Exception();

    }

} 