<?php

namespace Controller;

class GitAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function pull(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if ('demo.enter.ru' !== \App::request()->getHost()) {
            throw new \Exception\NotFoundException();
        }

        try {
            $result = shell_exec('cd "' . \App::config()->appDir . '" && git pull; git status');
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response('<pre>' . $result . '</pre>');
    }
}
