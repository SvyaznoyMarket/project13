<?php

namespace Controller\Compare;

class CompareAction {

    /** @var array */
    public $data;
//    private $user;
    private $session;
    private $compareSessionKey;

    public function __construct() {
//        $this->user = \App::user();
        $this->session = \App::session();
        $this->compareSessionKey = \App::config()->session['compareKey'];
        $this->data = (bool)$this->session->get($this->compareSessionKey) ? $this->session->get($this->compareSessionKey) : [];
    }

    public function execute() {

        $page = new \View\Compare\CompareLayout();
        return new \Http\Response($page->show());
    }

    public function add(\Http\Request $request, $productId) {

        $product = \RepositoryManager::product()->getEntityById($productId);

        if (!array_key_exists($product->getId(), $this->data)) {
            $this->data[$product->getId()] = [
                'id'            => $product->getId(),
                'ui'            => $product->getUi(),
//                'name'          => $product->getName(),
                'categoryId'    => $product->getLastCategory()->getId()
            ];
            $this->session->set($this->compareSessionKey, $this->data);
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse(['compare' => $this->session->get($this->compareSessionKey)]);
        }

        $referrer = $request->server->get('HTTP_REFERER');
        return new \Http\RedirectResponse($referrer);
    }

    public function delete(\Http\Request $request, $productId) {

        if (array_key_exists($productId, $this->data)) {
            unset($this->data[$productId]);
            $this->session->set($this->compareSessionKey, $this->data);
        }

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse(['compare' => $this->session->get($this->compareSessionKey)]);
        }

        return new \Http\RedirectResponse($request->server->get('HTTP_REFERER'));
    }

    public function clear() {
        $this->session->set($this->compareSessionKey, []);
    }

} 