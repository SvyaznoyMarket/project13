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
}