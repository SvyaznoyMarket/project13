<?php
namespace Controller\Refurbished;

class Action {

    public function subscribe(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException();
        }
        $response = array('success' => false);
        $subscriber = $request->get('subscriber');
        $response['request'] = $subscriber;
        $response['post_data'] = $_POST;
        if (isset($subscriber['name']) && (bool)$subscriber['name'] && isset($subscriber['email']) && (bool)$subscriber['email']) {
            $response = array('success' => true);
        }

        return new \Http\JsonResponse($response);

    }
}