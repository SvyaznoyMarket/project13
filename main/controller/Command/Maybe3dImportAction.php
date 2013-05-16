<?php

namespace Controller\Command;

class Maybe3dImportAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }

    /**
     * @param $pathToCms string
     * @throws \Exception|\RuntimeException
     */
    public function execute($pathToCms = null) {

        \App::logger()->debug('Exec ' . __METHOD__);
        $client = \App::coreClientV2();
        clearstatcache();

        /** @var  $allowedModels array */
        $allowedModels = [];
        /** @var $eans array */
        $eans = [];
        /** @var $productsById \Model\Product\BasicEntity[] */
        $productsByEan = [];

        if (!$pathToCms) $pathToCms = \App::config()->maybe3d['cmsFolder'];
        if (!is_dir($pathToCms)) throw new \RuntimeException('Указан не правильный путь до cms');

        $maybe3dUrl = \App::config()->maybe3d['xmlUrl'] . \App::config()->maybe3d['customerId'];

        try {
            /** @var  $models array */
            $models = simplexml_load_file($maybe3dUrl);
        } catch (\RuntimeException $e) {
            throw new \RuntimeException('Не удалось получить xml файл!');
        }

        $i = 0;
        foreach ($models as $model) {
            /** @var $model \SimpleXMLElement */
            if (!isset($model->mtd_id)) continue;
            $allowedModels[(string)$model->mtd_id][] = [
                'ean' => (string)$model->value,
                'file_name' => (string)$model->mtd_filename,
            ];
            $eans[(string)$model->value] = true;
            if ($i == 50) {
                $eans = array_keys($eans);
                \RepositoryManager::product()->prepareCollectionByEan($eans, null, function($data) use (&$productsByEan) {
                    foreach ($data as $item) {
                        if (isset($item['ean']) && is_array($item['ean'])) {
                            foreach ($item['ean'] as $ean) {
                                if (isset($productsByEan[(string)$ean]) && $productsByEan[(string)$ean]->getId() !=  (int)$item['id']) {
                                    \App::logger()->error("Same EAN codes on products id:  {$productsByEan[(string)$ean]->getId()} and {$item['id']}");
                                }
                                $productsByEan[(string)$ean] = new \Model\Product\BasicEntity($item);
                            }
                        }
                    }
                }, function(\Exception $e) {
                    \App::exception()->remove($e);
                    \App::logger()->error('Fail maybe3d get models');
                });
                $client->execute();
                $i = 0;
                $eans = [];
            } else $i++;
        }

        if (!count($productsByEan)) throw new \RuntimeException('Нет ни одной модели для обновления!');

        $completeCount = 0;
        $noSwfCount = 0;
        $newestCount = 0;

        foreach ($allowedModels as $mtd_id => $allowedModel) {
            if (!is_array($allowedModel)) continue;
            foreach ($allowedModel as $allowedSingleModel) {
                $bNewest = false;
                if (isset($productsByEan[$allowedSingleModel['ean']])) {
                    $swfUrl = \App::config()->maybe3d['swfUrl'].$allowedSingleModel['file_name'].'/'.$allowedSingleModel['file_name'].'.swf';
                    $fileHeaders = @get_headers($swfUrl);
                    if ($fileHeaders[0] != 'HTTP/1.1 404 Not Found') {
                        $file_name = rtrim($pathToCms, '\\') . '\\' .$productsByEan[$allowedSingleModel['ean']]->getId() . '.json';
                        if (file_exists($file_name)) {
                            try {
                                $json = file_get_contents($file_name);
                                try {
                                    /** @var  $product array */
                                    $product = json_decode($json, true);
                                    if (isset($product[0])) {
                                        $product = $product[0];
                                    } else \App::logger()->error("Empty json file: {$file_name}");
                                    if (!isset($product['content'])) {
                                        $product['content'] = '';
                                    }
                                    if (!isset($product['maybe3d'])) $bNewest = true;
                                    $product['maybe3d'] = $swfUrl;
                                    $jsonInsert = json_encode([$product]);
                                    try {
                                        file_put_contents($file_name, $jsonInsert);
                                    } catch (\Exception $e) {
                                        \App::logger()->error("Fail save json to file: {$file_name}");
                                    }
                                    if ($bNewest) $newestCount++;
                                    $completeCount++;
                                    $completeProducts[$productsByEan[$allowedSingleModel['ean']]->getId()] = true;
                                } catch (\Exception $e) {
                                    \App::logger()->error("Fail json decode file: {$file_name}");
                                }
                            } catch (\Exception $e) {
                                \App::logger()->error("Fail open file: {$file_name}");
                            }
                        } else {
                            $json = json_encode([['content' => '', 'maybe3d' => $swfUrl]]);
                            try {
                                file_put_contents($file_name, $json);
                            } catch (\Exception $e) {
                                \App::logger()->error("Fail save json to file: {$file_name}");
                            }
                            $completeCount++;
                            $newestCount++;
                            $completeProducts[$productsByEan[$allowedSingleModel['ean']]->getId()] = true;
                        }
                    } else {
                        $noSwfCount++;
                        \App::logger()->error(".SWF not exists for model: ean => {$allowedSingleModel['ean']}, mtd => {$mtd_id}, name => {$allowedSingleModel['file_name']}");
                    }
                    break;
                }
            }
        }

        $deleteCount=0;
        $productFiles = glob($pathToCms.'*.{json,JSON}', GLOB_BRACE);
        if (!count($productFiles)) throw new \RuntimeException("В папке {$pathToCms} нет ни одного json для обновления!");
        foreach ($productFiles as $productFile) {
            $baseName = str_ireplace(['.JSON','.json'], '', basename($productFile));
            if ((int)$baseName &&  !isset($completeProducts[(int)$baseName])) {
                if (file_exists($productFile)) {
                    try {
                        $json = file_get_contents($productFile);
                        try {
                            /** @var  $product array */
                            $product = json_decode($json, true);
                            if (isset($product[0])) {
                                if (isset($product[0]['maybe3d'])) {
                                    unset($product[0]['maybe3d']);
                                    $json = json_encode($product);
                                    try {
                                        file_put_contents($productFile, $json);
                                    } catch (\Exception $e) {
                                        \App::logger()->error("Fail save json to file (for delete maybe3d url): {$productFile}");
                                    }
                                    $deleteCount++;
                                }
                            }
                        } catch (\Exception $e) {
                            \App::logger()->error("Fail decode json from file (for delete maybe3d url): {$productFile}");
                        }
                    } catch (\Exception $e) {
                        \App::logger()->error("Fail open file (for delete maybe3d url): {$productFile}");
                    }
                }
            }
        }

        print "All count: ".count($allowedModels)."\n";
        print "Added models: {$completeCount}\n";
        print "Newest models: {$newestCount}\n";
        print "Not existing .SWF: {$noSwfCount}\n";
        print "Deleted models: {$deleteCount}";

    }
}