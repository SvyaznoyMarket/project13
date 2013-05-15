<?php

namespace Controller\Cron;

class Action {

    public function execute(\Http\Request $request, $task) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $consoleFilepath = \App::config()->appDir . '/console.php';
        $host = $request->getHost();

        $dateStart = new \DateTime();

        switch ($task) {
            case 'report':
                $reportDir = \App::config()->appDir . '/report';
                $sourceCsvDir = \App::config()->cmsDir . '/v1/logs/accessory';

                if(!is_file($reportDir . '/' . $dateStart->format('YmdH') . '.lock')) {
                    system("php ". $consoleFilepath . " Command/BuSeoReportAction generate " . \App::$env . " > /dev/null &");
                    $content = '<h4>Задание планировщика "'.$task.'" запущено</h4>';
                } else {
                    $content = '<h4>Задание планировщика "'.$task.'" уже было запущено ранее</h4>';
                }

                $content .= '<div class="mb15">После завершения генерации отчеты будут доступны по ссылкам:</div><ul class="mb25">';

                foreach (scandir($sourceCsvDir) as $file) {
                    if(preg_match('/^(.+)\.csv$/', $file, $matches)) {
                        $rootCategory = $matches[1];
                        $content .= "<li><a href='http://".$host."/report/" . $dateStart->format('YmdH') . '_' . $rootCategory . "_accessories_bu.csv'>Скачать отчет для БЮ (" . $rootCategory . ")</a></li>".
                        "<li><a href='http://".$host."/report/" . $dateStart->format('YmdH') . '_' . $rootCategory . "_accessories_seo.csv'>Скачать отчет для SEO (" . $rootCategory . ")</a></li>";
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