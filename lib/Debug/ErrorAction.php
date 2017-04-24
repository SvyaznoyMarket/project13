<?php

namespace Debug;

class ErrorAction {
    public function execute() {
        $request = \App::request();

        $logger = \App::logger();
        if ($request->isXmlHttpRequest()) {
            $response = new \Http\JsonResponse(['success' => false, 'trace' => $logger->getMessages()]);
        } else {
            $response = new \Http\Response();

            $messages = $logger->getMessages();
            foreach ($messages as &$message) {
                $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
                $message = preg_replace('/#(\d+) /', '<span style="color: #ff0000">#${1} </span>', $message);
                $message = str_replace('error', '<span style="color: #ff3333">error</span>', $message);
                $message = str_replace('info', '<span style="color: #00ffff">info</span>', $message);
                $message = str_replace('Stack trace:', '<span style="color: #ff3333">Stack trace:</span>', $message);
            } if (isset($message)) unset($message);

            $error = error_get_last();
            $error = isset($error['message']) ? json_encode($error,  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) : json_encode(\App::exception()->all(),  JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            $response->setContent('<link href="/css/global.min.css" type="text/css" rel="stylesheet" /><link href="/styles/global.min.css" type="text/css" rel="stylesheet" /><script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js" type="text/javascript"></script><pre style="overflow: auto; background: #333333; color: #fcfcfc; padding: 8px 12px; border-radius: 5px; font-size: 14px; font-weight: normal; font-family: Courier New; box-shadow: 0 0 10px rgba(0,0,0,0.5);">' . '<pre style="font-size: 15px; font-weight: normal; color: #ff3333;">' . $error . '</pre><hr style="border: 0 none; height: 1px; background: #cccccc;" />' . implode("\n", $messages) . '</pre>');
        }

        $response->setStatusCode(500);

        return $response;
    }
}