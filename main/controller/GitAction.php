<?php

namespace Controller;

class GitAction {
    public function __construct() {
        if (in_array(\App::request()->getHost(), ['www.enter.ru', 'enter.ru'])) {
            throw new \Exception\NotFoundException();
        }
    }

    /**
     * @return \Http\Response
     */
    public function pull() {
        //\App::logger()->debug('Exec ' . __METHOD__);

        try {
            $result = shell_exec('cd "' . \App::config()->appDir . '" && (git fetch; git status; git pull; git status)');
        } catch (\Exception $e) {
            $result = (string)$e;
        }

        $result = str_replace('On branch', '<b>On branch</b>', $result);


        return new \Http\Response('<pre>' . $result . '</pre>');
    }

    /**
     * @param $version
     * @return \Http\Response
     */
    public function checkout($version) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $version = 'v' . (int)$version;

        try {
            $result = shell_exec('cd "' . \App::config()->appDir . '" && (git fetch; git status; git checkout ' . $version . '; git pull; git status)');
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response('<pre>' . $result . '</pre>');
    }
}
