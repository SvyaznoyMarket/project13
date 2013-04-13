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

        \App::logger()->debug('Exec ' . __METHOD__);

        $result = '';
        try {
            $result = shell_exec(sprintf('cd %s && tail -n 10000 app.log | grep %s -B 100',
                \App::config()->logDir,
                $id
            ));
            $result = strtr($result, [
                $id     => '<span style="color: #00ff00;">' . $id . '</span>',
                'error' => '<span style="color: #ff0000;">error</span>',
            ]);
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response($result);
    }
}
