<?php

namespace Controller;

class DebugAction {

    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     */
    public function info(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $data = [];

        $gitData = [
            'version' => trim(shell_exec(sprintf('cd %s && git rev-parse --abbrev-ref HEAD', realpath(\App::config()->appDir)))),
            'tag'     => trim(shell_exec(sprintf('cd %s && git describe --always --tag', realpath(\App::config()->appDir)))),
        ];
        $gitData['url'] = 'https://github.com/SvyaznoyMarket/project13/tree/' . $gitData['version'];

        $data['result']['git'] = $gitData;

        return new \Http\JsonResponse($data);
    }

    public function session() {
        $data = \App::session()->all();
        unset($data['__prevDebug__']);
        return new \Http\JsonResponse($data);
    }

}