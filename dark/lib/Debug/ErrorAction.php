<?php

namespace Debug;

class ErrorAction {
    public function execute() {
        $request = \App::request();

        $logger = \App::logger();
        if ($request->isXmlHttpRequest()) {
            $response = new \Http\JsonResponse(array('success' => false, 'trace' => $logger->getMessages()));
        } else {
            $response = new \Http\Response();

            $messages = $logger->getMessages();
            foreach ($messages as &$message) {
                $message = preg_replace('/#(\d+) /', '<span style="color: #ff0000">#${1} </span>', $message);
                $message = implode(' ', $message);
                $message = str_replace(' error ', '<span style="color: #ff0000"> error </span>', $message);
                $message = str_replace(' info ', '<span style="color: #00ffff"> info </span>', $message);
                $message = str_replace('Stack trace:', '<span style="color: #ff0000">Stack trace:</span>', $message);
            } if (isset($message)) unset($message);

            $response->setContent('<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script><pre style="overflow: auto; background: #333333; color: #fcfcfc; padding: 8px 12px; border-radius: 5px; font-size: 14px; font-weight: normal; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">' . implode("\n", $messages) . '</pre>');
        }

        $response->setStatusCode(500);

        return $response;
    }
}