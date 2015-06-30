<?php

namespace Controller;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class EventAction {
    use CurlTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function push(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $eventName = $request->get('name');
        if (!$eventName) {
            throw new \Exception('Не получено название события');
        }

        $data = is_array($request->get('data')) ? $request->get('data') : [];

        if ('pushOrderStep') {
            (new Query\Event\PushOrderStep($data))->prepare();
        }

        $this->getCurl()->execute();

        return new \Http\JsonResponse([]);
    }
}
