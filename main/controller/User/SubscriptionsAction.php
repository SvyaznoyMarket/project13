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

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse|\Http\Response
     */
    public function execute(\Http\Request $request) {
        if ($request->isMethod('post')) {
            $responseData = [];

            try {
                $this->setData($request);
                //$this->session->flash(['type' => 'success', 'message' => 'Параметры подписок сохранены']);
                $responseData['success'] = true;
            } catch (\Curl\Exception $e) {
                \App::logger()->error($e, ['curl', 'error']);
                //$this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
                $responseData['error'] = $e;

                \App::exception()->remove($e);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['error']);
                //$this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
                $responseData['error'] = $e;
            }
            return
                $request->isXmlHttpRequest()
                ? new \Http\JsonResponse($responseData)
                : new \Http\RedirectResponse(\App::router()->generate('user.subscriptions'))
            ;
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData()
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\SubscriptionsPage();
        $page->setParam('subscriptions', $data['subscriptions']);
        $page->setParam('subscriptionsGroupedByChannel', $data['subscriptionsGroupedByChannel']);
        $page->setParam('channelsById', $data['channelsById']);
        //$page->setParam('flash', $this->session->flash());

        return new \Http\Response($page->show());

    }

    /** Возвращает массив подписок пользователя
     * @return array
     */
    private function getData() {

        //\App::logger()->debug('Exec ' . __METHOD__);

        /** @var \Model\User\SubscriptionEntity[] $subscriptions */
        $subscriptions = [];
        /** @var \Model\Subscribe\Channel\Entity[] $channelsById */
        $channelsById = [];
        $subscriptionsGroupedByChannel = [];

        $this->client->addQuery(
            'subscribe/get',
            [
                'token' => $this->user->getToken(),
            ],
            [],
            function ($data) use (&$subscriptions) {
                foreach ($data as $item) {
                    $subscriptions[] = new \Model\User\SubscriptionEntity($item);
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
            function ($data) use (&$channelsById) {
                foreach ($data as $item) {
                    if (empty($item['id'])) continue;

                    $channel = new \Model\Subscribe\Channel\Entity($item);
                    $channelsById[$channel->id] = $channel;
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        $this->client->execute();

        $userEmail = $this->user->getEntity()->getEmail() ?: null;
        foreach ($subscriptions as $subscription) {
            if (('email' === $subscription->type) && $userEmail && ($userEmail !== $subscription->email)) {
                // пропустить подписки, у которых email не совпадает с email-ом пользователя
                continue;
            }

            /** @var \Model\Subscribe\Channel\Entity|null $channel */
            $channel = ($subscription->channelId && isset($channelsById[$subscription->channelId])) ? $channelsById[$subscription->channelId] : null;
            if ($channel) {
                $subscription->channel = $channel;
                $subscriptionsGroupedByChannel[$channel->id][] = $subscription;
            }
        }

        return [
            'subscriptions'                 => $subscriptions,
            'subscriptionsGroupedByChannel' => $subscriptionsGroupedByChannel,
            'channelsById'                  => $channelsById,
        ];
    }

    /** Сохраняет данные подписок
     * @param \Http\Request $request
     * @throws \Exception
     */
    private function setData(\Http\Request $request) {

        $formData = is_array($request->get('subscribe')) ? $request->get('subscribe') : [];
        $isDelete = (bool)$request->get('delete');

        $formData += [
            'is_confirmed' => true,
        ];

        if ($isDelete) {
            $response = $this->client->query(
                'subscribe/delete',
                [
                    'token' => $this->user->getToken(),
                ],
                [
                    $formData,
                ]
            );
        } else {
            $response = $this->client->query(
                'subscribe/set',
                [
                    'token' => $this->user->getToken(),
                ],
                [
                    $formData,
                ]
            );
        }

        if (!isset($response['confirmed']) || $response['confirmed'] == false) {
            throw new \Exception('Не удалось сохранить подписку');
        }

    }

} 