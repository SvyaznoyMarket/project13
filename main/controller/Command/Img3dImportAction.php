<?php

namespace Controller\Command;

class Img3dImportAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }

    /**
     * @param $pathToData string
     * @param $pathToCms string
     * @throws \Exception|\RuntimeException
     */
    public function execute($pathToData, $pathToCms = null) {

        \App::logger()->debug('Exec ' . __METHOD__);
        clearstatcache();

        if (!is_dir($pathToData)) throw new \RuntimeException('Указан не правильный путь до папки с исходными json-файлами');

        if (!$pathToCms) $pathToCms = \App::config()->img3d['cmsFolder'];
        if (!is_dir($pathToCms)) throw new \RuntimeException('Указан не правильный путь до cms');

        print PHP_EOL . PHP_EOL;

        $copiedCount = 0;
        $inFilesCount = 0;
        $noOutFilesCount = 0;

        foreach (scandir($pathToData) as $inJsonFile) {
            $inJsonFilePath = $pathToData . $inJsonFile;
            $outJsonFilePath = $pathToCms . $inJsonFile;

            if (is_file(($inJsonFilePath)) && file_exists($inJsonFilePath)) {
                $inFilesCount++;
                if(file_exists($outJsonFilePath)) {
                    try {
                        $inJson = file_get_contents($inJsonFilePath);
                        $outJson = file_get_contents($outJsonFilePath);

                        try {
                            /** @var  $productJson array */
                            $inProductJson = json_decode($inJson, true);
                            $outProductJson = json_decode($outJson, true);

                            if (isset($outProductJson[0])) {
                                $outProductJson[0]['img3d'] = $inProductJson;
                                $outJson = json_encode($outProductJson, JSON_PRETTY_PRINT);
                                try {
                                    file_put_contents($outJsonFilePath, $outJson);
                                    $copiedCount++;
                                } catch (\Exception $e) {
                                    \App::logger()->error("Fail save json to file: {$outJsonFilePath}");
                                }
                            }
                        } catch (\Exception $e) {
                            \App::logger()->error("Fail decode json from one of files: {$inJsonFilePath} or {$outJsonFilePath}");
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error("Fail open file one of files: {$inJsonFilePath} or {$outJsonFilePath} ");
                    }
                } else {
                    $noOutFilesCount++;
                }
            }
        }

        print "Total source files: $inFilesCount" . PHP_EOL;
        print "Copied data from source files: $copiedCount" . PHP_EOL;
        print "Output files not found: $noOutFilesCount" . PHP_EOL;
    }
}