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
                $id      => '<span style="color: green;">' . $id . '</span>',
                'error'  => '<span style="color: red;">error</span>',
                'warn'   => '<span style="color: orange;">warn</span>',
                '+curl'  => '<span style="color: deeppink;">+curl</span>',
                '+view'  => '<span style="color: lightgreen;">+view</span>',
                '+order' => '<span style="color: #4cc5e8;">+order</span>',
            ]);
        } catch (\Exception $e) {
            $result = (string)$e;
        }


        return new \Http\Response($result);
    }
}
