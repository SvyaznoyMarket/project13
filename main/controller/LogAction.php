<?php

namespace Controller;

class LogAction {
    /**
     * @param $id
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($id) {
        if ('local' !== \App::$env) {
            throw new \Exception\NotFoundException();
        }

        //\App::logger()->debug('Exec ' . __METHOD__);

        $result = '';
        try {
            $result = shell_exec(sprintf('cd %s && tail -n 10000 app.log | grep %s -B 100',
                \App::config()->logDir,
                $id
            ));
            /*
            $result = implode(PHP_EOL, array_map(function($line) {
                $line = json_decode($line, true);
                return json_encode($line, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }, explode(PHP_EOL, $result)));
            */
            $result = strtr($result, [
                $id      => '<span style="color: green;">' . $id . '</span>',
                'uri'    => '<span style="color: greenyellow; font-weight: bold">uri</span>',
                'error'  => '<span style="color: red;">error</span>',
                'Fail'   => '<span style="color: red;">Fail</span>',
                'warn'   => '<span style="color: orange;">warn</span>',
                '+curl'  => '<span style="color: deeppink;">+curl</span>',
                'Start'  => '<span style="color: #00ffff;">Start</span>',
                'End'    => '<span style="color: #00ffff;">End</span>',
                '+view'  => '<span style="color: lightgreen;">+view</span>',
                '+order' => '<span style="color: #4cc5e8;">+order</span>',
                \App::config()->coreV2['url']  => '<span style="color: deeppink;">' . \App::config()->coreV2['url'] . '</span>',
            ]);
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response($result);
    }
}
