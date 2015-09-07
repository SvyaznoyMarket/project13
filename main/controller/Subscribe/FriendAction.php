<?php

namespace Controller\Subscribe;

use EnterModel as Model;

class FriendAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function show(\Http\Request $request) {
        $flashData = array_merge(
            [
                'success' => null,
                'errors'  => [],
            ],
            (array)\App::session()->flash()
        );

        /** @var Model\Error|null $error */
        $error = isset($flashData['errors'][0]['id']) ? (new Model\Error())->importFromArray($flashData['errors'][0]) : null;
        $success = isset($flashData['success']) && (true === $flashData['success']);

        $page = new \View\Subscribe\Friend\ShowPage();
        $page->setParam('alreadySubscribed', $error ? ('already_subscribed' === $error->id) : false);
        $page->setParam('successfullySubscribed', $success);

        $response = new \Http\Response($page->show());

        return $response;
    }

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception
     */
    public function create(\Http\Request $request) {
        $session = \App::session();
        $userEntity = \App::user()->getEntity();
        $channelId = (int)$request->get('channel', 1);

        $flashData = [
            'success' => null,
            'errors'  => [],
        ];

        $controller = new \EnterApplication\Action\Subscribe\Create();
        $controllerRequest = $controller->createRequest();
        $controllerRequest->userToken = $userEntity ? $userEntity->getToken() : null;
        $controllerRequest->channelId = $channelId;
        $controllerRequest->email = is_string($request->get('email')) ? $request->get('email') : null;

        $controllerResponse = $controller->execute($controllerRequest);

        if ($error = $controllerResponse->errors->reset()) {
            $flashData['success'] = false;
            $flashData['errors'] = $controllerResponse->errors->exportToArray();
        } else {
            $flashData['success'] = true;
        }

        $session->flash($flashData);

        return new \Http\RedirectResponse(\App::router()->generate('subscribe.friend.show'));
    }
}
