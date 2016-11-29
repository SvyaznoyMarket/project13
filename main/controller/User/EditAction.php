<?php

namespace Controller\User;

class EditAction extends PrivateAction {

    private $client;
    private $user;
    private $session;

    public function __construct() {
        parent::__construct();
        $this->client = \App::coreClientV2();
        $this->user = \App::user()->getEntity();
        $this->session = \App::session();
    }

    public function execute(\Http\Request $request) {
        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['private']) ? $entity->value['private'] : [];
            }

            return true;
        });

        $this->client->execute();

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData($request)
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\EditPage();
        $page->setParam('form', $data['form']);
        $page->setParam('flash', $this->session->flash());
        $page->setParam('redirect', $data['redirect']);
        $page->setParam('bonusCards', $data['bonusCards']);
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }

    private function getData(\Http\Request $request) {
        $form = new \View\User\EditForm();
        $form->fromEntity($this->user);
        $form->setMobilePhone(preg_replace('/^8/', '+7', $form->getMobilePhone()));
        $form->setHomePhone(preg_replace('/^8/', '+7', $form->getHomePhone()));

        $message = $this->session->get('flash');
        $this->session->remove('flash');

        $redirect = $request->get('redirect_to')
            ? $request->get('redirect_to')
            : \App::router()->generateUrl('user.edit');

        if(!preg_match('/^(\/|http).*/i', $redirect)) {
            $redirect = 'http://' . $redirect;
        }

        $bonusCards = [];

        try {
            $bonusCards = \RepositoryManager::bonusCard()->getCollection();
        } catch (\Exception $e) {
            \App::logger()->error($e);
            \App::exception()->remove($e);
        }

        return ['form' => $form, 'message' => $message, 'redirect' => $redirect, 'bonusCards' => $bonusCards];
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function editSclubNumber(\Http\Request $request) {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        try {
            $number = trim($request->get('number'));
            if (!$number) {
                throw new \Exception('Не передан номер Связного');
            }

            $sclubId = \Model\Order\BonusCard\Entity::SVYAZNOY_ID;
            $userBonusCards = $this->user->getBonusCard() ?: [];

            // подставляем новый номер Связного
            $isEdit = false;
            foreach ($userBonusCards as $key => $card) {
                if (isset($card['bonus_card_id']) && $card['bonus_card_id'] == $sclubId) {
                    $userBonusCards[$key]['number'] = $number;
                    $isEdit = true;
                }
            }

            if (!$isEdit) {
                $userBonusCards[] = ['bonus_card_id' => $sclubId, 'number' => $number];
            }

            // формируем массив номеров бонусных карт
            $bonusCardNumbers = [];
            if (is_array($userBonusCards) && !empty($userBonusCards)) {
                $bonusCardNumbers = array_filter(array_map(function($card){
                    return isset($card['number']) && !empty($card['number']) ? $card['number'] : null;
                }, $userBonusCards));
            }

            $result = $this->client->query('user/update', ['token' => \App::user()->getToken()],
                ['bonus_card' => $bonusCardNumbers], \App::config()->coreV2['hugeTimeout']);

            if (!isset($result['confirmed']) || !$result['confirmed']) {
                throw new \Exception('Не получен ответ от сервера.');
            }

            $responseData = ['success' => true];

        } catch(\Exception $e) {
            \App::exception()->remove($e);
            \App::logger()->error($e);

            $responseData = [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }

        return new \Http\JsonResponse($responseData);
    }
}