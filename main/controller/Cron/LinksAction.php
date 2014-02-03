<?php

namespace Controller\Cron;

class LinksAction {

    public function execute(\Http\Request $request, $task) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if(!$request->isXmlHttpRequest()) throw new \Exception\NotFoundException();

        $host = $request->getHost();

        switch ($task) {
            case 'report':
                $reportDir = \App::config()->appDir . '/report';
                $content = '<ul class="mb25">';
                foreach (scandir($reportDir) as $file) {
                    if(preg_match('/^(.+)\.csv$/', $file, $matches)) {
                        $content .= "<li><a href='http://".$host."/report/" . $file . "'>" . $file . "</a></li>";
                    }
                }
                $content .= '</ul>';
                break;
            
            default:
                throw new \Exception\NotFoundException();
                break;
        }

        return new \Http\JsonResponse(['success' => true, 'data' => $content]);
    }

}