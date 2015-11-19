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
            $responseData = [];

            try {
                $this->setData($request);
                $this->session->flash(['type' => 'success', 'message' => 'Параметры подписок сохранены']);
                $responseData['success'] = true;
            } catch (\Curl\Exception $e) {
                \App::logger()->error($e, ['curl', 'error']);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
                $responseData['error'] = $e;

                \App::exception()->remove($e);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['error']);
                $this->session->flash(['type' => 'error', 'message' => 'Не удалось сохранить параметры подписок']);
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
        $page->setParam('flash', $this->session->flash());

        return new \Http\Response($page->show());

    }

    /** Возвращает массив подписок пользователя
     * @return array
     */
    private function getData() {

        //\App::logger()->debug('Exec ' . __METHOD__);

        $subscriptions = [];
        $channelCollection = [];

        $this->client->addQuery(
            'subscribe/get',
            [
                'token' => $this->user->getToken(),
            ],
            [],
            function ($data) use (&$subscriptions) {
                foreach ($data as $channel) {
                    $subscriptions[] = new \Model\User\SubscriptionEntity($channel);
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
                foreach ($data as $item) {
                    $channelCollection[] = new \Model\Subscribe\Channel\Entity($item);
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        $this->client->execute();

        $userEmail = $this->user->getEntity()->getEmail() ?: null;
        foreach ($subscriptions as $subscription) {
            /** @var \Model\User\SubscriptionEntity $subscription */
            if (('email' === $subscription->type) && $userEmail && ($userEmail !== $subscription->email)) {
                // пропустить подписки, у которых email не совпадает с email-ом пользователя
                continue;
            }

            if ($subscription->channelId) {
                $channels = array_filter($channelCollection, function (\Model\Subscribe\Channel\Entity $channel) use ($subscription) {
                    return $subscription->channelId == $channel->id;
                });
                $subscription->channel = reset($channels);
            }
        }

        return ['subscriptions' => $subscriptions];

    }

    /** Сохраняет данные подписок
     * @param \Http\Request $request
     * @throws \Exception
     */
    private function setData(\Http\Request $request) {

        $formData = $request->get('subscribe');

        $response = $this->client->query(
            'subscribe/set',
            [
                'token' => $this->user->getToken(),
            ],
            [
                $formData,
            ]
        );

        if (!isset($response['confirmed']) || $response['confirmed'] == false) {
            throw new \Exception('Не удалось сохранить подписку');
        }

    }

} 