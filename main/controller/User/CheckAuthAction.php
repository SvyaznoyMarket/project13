<?php

namespace Controller\User;

use EnterApplication\CurlTrait;

class CheckAuthAction
{

    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function execute(\Http\Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception('Неверный запрос', 400);
        }

        $redirectTo = (is_string($request->get('redirect_to')) && !empty($request->get('redirect_to'))) ? $request->get('redirect_to') : null;

        $responseData = [];

        if (\App::user()->getEntity()) {
            $responseData['redirect'] = $redirectTo;
        }

        return new \Http\JsonResponse($responseData);
    }
}