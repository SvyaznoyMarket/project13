<?php

namespace Controller\Cart;

class ClearAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse|\Http\RedirectResponse
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        \App::user()->getCart()->clear();

        return $request->isXmlHttpRequest()
            ? new \Http\JsonResponse(['success' => true])
            : new \Http\RedirectResponse($request->headers->get('referer') ?: \App::router()->generate('homepage'));
    }
}