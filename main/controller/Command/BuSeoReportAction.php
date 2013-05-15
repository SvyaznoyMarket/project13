<?php

namespace Controller\Command;

class BuSeoReportAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }


    /**
     * Создает отчеты для Бизнес-юнитов и для SEO-подрядчиков о состоянии аксессуаров
     *
     */
    public static function generate($max_parts = 0) {
        system('cd ' . \App::config()->cmsDir . ' && git pull && git checkout sandbox');
        ini_set("auto_detect_line_endings", true);
        $repository = \RepositoryManager::product();
        $sourceCsvDir = \App::config()->cmsDir . '/v1/logs/accessory';
        if(!is_dir($sourceCsvDir)){
            throw new \Exception(sprintf('BuSeoReport: не найден каталог с исходными файлами csv %s', $sourceCsvDir));
        }
        $client = \App::coreClientV2();
        $inCsvDelimiter = ",";
        $outCsvDelimiter = "\t";

        $dateStart = new \DateTime();

        // если отчет в процессе генерации, то выходим, иначе создаем лок-файл
        $lockFilepath = \App::config()->appDir . '/report/' . $dateStart->format('YmdH') . '.lock';
        if(is_file($lockFilepath)) return;
        touch($lockFilepath);

        foreach (scandir($sourceCsvDir) as $file) {
            if(preg_match('/^(.+)\.csv$/', $file, $matches)) {
                $rootCategory = $matches[1];
                $reportBuFilepath = \App::config()->appDir . '/report/' . $dateStart->format('YmdH') . '_' . $rootCategory . '_accessories_bu.csv';
                $reportSeoFilepath = \App::config()->appDir . '/report/' . $dateStart->format('YmdH') . '_' . $rootCategory . '_accessories_seo.csv';
                $sourceCsvFilepath = \App::config()->appDir . '/report/source/' . $file;
                $benchmarkFilepath = \App::config()->appDir . '/report/source/benchmark.txt';
                $reportBu = fopen($reportBuFilepath, 'a');
                $reportSeo = fopen($reportSeoFilepath, 'a');

                //шапки выходных csv
                fwrite($reportBu, "Тип проблемы" . $outCsvDelimiter . "URL категории товара" . $outCsvDelimiter . "id" . $outCsvDelimiter . "bar_code" . $outCsvDelimiter . "Название товара" . $outCsvDelimiter . "URL категории аксессуара" . $outCsvDelimiter . "accessories bar_code" . $outCsvDelimiter . "Название товара-аксессуара" . $outCsvDelimiter . "Значение флага is_buyable\n");
                fwrite($reportSeo, "URL родительской категории" . $outCsvDelimiter . "Кол-во товаров в родительской категории" . $outCsvDelimiter . "URL категории аксессуара" . $outCsvDelimiter . "Кол-во товаров в родительской категории к которым привязан хотя бы один аксессуар из категории аксессуара\n");

                // парсим csv с входными данными по продуктам
                $productsCsv = [];
                if (($sourceCsv = fopen($sourceCsvFilepath, "r")) !== FALSE) {
                    while (($data = fgetcsv($sourceCsv, 0, $inCsvDelimiter)) !== FALSE) {
                        if(!empty($data[0])) {
                            $productsCsv[$data[0]] = [
                                // 'article' => empty($data[1]) ? '' : $data[1],
                                'barcode' => empty($data[2]) ? '' : $data[2],
                                // 'url' => empty($data[3]) ? '' : $data[3],
                            ];
                        }
                    }
                    fclose($sourceCsv);
                }

                // аккумулятор для SEO-данных
                $categoryProductsData = [];

                // проводим анализ по частям для снижения нагрузки
                $step = 100;
                $part = 1;

$timeGetAccessories = 0;
$timeGetJson = 0;
$timeExistingCategories = 0;

                while ($part <= (int)ceil(count($productsCsv) / $step)) {
                    if($max_parts && $part > $max_parts) break;

                    // получение массива товаров и определение не найденных товаров
                    $productIdsPart = array_slice(array_keys($productsCsv), ($part - 1) * $step, $step);
                    $products = $repository->getCollectionById($productIdsPart);
                    $productIdsPartApi = array_map(function($product){ return $product->getId();}, $products);
                    $notFoundProductIds = array_diff($productIdsPart, $productIdsPartApi);
                    foreach ($notFoundProductIds as $notFoundProductId) {
                        fwrite($reportBu, "Не найден товар" . $outCsvDelimiter . $outCsvDelimiter . $notFoundProductId . $outCsvDelimiter . $productsCsv[$notFoundProductId]['barcode'] . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . "\n");
                    }

                    foreach ($products as $product) {
                        $isBuyable = $product->getIsBuyable() ? 'true' : 'false';

                        // текущие аксессуары товара
$date1 = new \DateTime();
                        $accessories = $repository->getAccessories($product);
$date2 = new \DateTime();
$timeGetAccessories += $date2->getTimestamp() - $date1->getTimestamp();

                        // массив токенов категорий, разрешенных в json
$date1 = new \DateTime();
                        $jsonCategoryToken = $repository->getJsonCategoryToken($product);
$date2 = new \DateTime();
$timeGetJson += $date2->getTimestamp() - $date1->getTimestamp();

                        // если в json не заданы категории аксессуаров
                        if(empty($jsonCategoryToken)) {
                            fwrite($reportBu, "Не установлены категории аксессуаров в json" . $outCsvDelimiter . $outCsvDelimiter . $product->getId() . $outCsvDelimiter . $product->getBarcode() . $outCsvDelimiter . $product->getName() . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter  . $outCsvDelimiter . $isBuyable."\n");
                        }
 
                        // получаем родительскую категорию товара
                        $productCategories = $product->getCategory();
                        $productParentCategory = end($productCategories);

                        // если родительская категория не задана
                        if(empty($productParentCategory)) {
                            fwrite($reportBu, "Не установлена категория продукта" . $outCsvDelimiter . $outCsvDelimiter . $product->getId() . $outCsvDelimiter . $product->getBarcode() . $outCsvDelimiter . $product->getName() . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $isBuyable."\n");
                        }

                        // фильтруем аксессуары товара, оставляя только разрешенные в json
                        $accessoriesGrouped = $repository->groupByCategory($repository->filterAccessoriesByJson($accessories, $jsonCategoryToken), 'accessories');
                        // получаем токены разрешенных в json категорий, для которых привязан хотя бы 1 аксессуар
                        $accessoryCategoryTokens = array_keys($accessoriesGrouped);
                        // получаем объекты категорий на основе указанных в json токенов
$date1 = new \DateTime();
                        $existingCategories = \RepositoryManager::productCategory()->getCollectionByToken($jsonCategoryToken);
$date2 = new \DateTime();
$timeExistingCategories += $date2->getTimestamp() - $date1->getTimestamp();
                        foreach ($jsonCategoryToken as $categoryToken) {
                            // проверяем существуют ли категории, указанные в json
                            if(!in_array($categoryToken, array_map(function($category){return $category->getToken();}, $existingCategories))) {
                                fwrite($reportBu, "Категории не существует (json)" . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $isBuyable."\n");
                            }

                            // проверяем есть ли привязанные к товару аксессуары из данной категории json
                            if(!in_array($categoryToken, $accessoryCategoryTokens)) {
                                fwrite($reportBu, "Не привязан аксессуар из категории в json" . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '') . $outCsvDelimiter . $product->getId() . $outCsvDelimiter . $product->getBarcode() . $outCsvDelimiter . $product->getName() . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter .$isBuyable."\n");
                            }

                            // проверяем количество привязанных к товару аксессуаров из данной категории json
                            if(in_array($categoryToken, $accessoryCategoryTokens) && count($accessoriesGrouped[$categoryToken]['accessories']) < 4) {
                                fwrite($reportBu, "В категории меньше четырех аксессуаров (json)" . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '') . $outCsvDelimiter . $product->getId() . $outCsvDelimiter . $product->getBarcode() . $outCsvDelimiter . $product->getName() . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".$categoryToken . $outCsvDelimiter . $outCsvDelimiter . $outCsvDelimiter . $isBuyable."\n");
                            }
                        }

                        // проверяем есть ли у продукта аксессуары из категорий, не указанных в json
                        $accessoriesNotInJson = $repository->filterAccessoriesNotInJson($accessories, $jsonCategoryToken);
                        foreach ($accessoriesNotInJson as $accessoryNotInJson) {
                            $accessoryCategories = $accessoryNotInJson->getCategory();
                            $accessoryParentCategory = end($accessoryCategories);

                            fwrite($reportBu, "Привязан неверный аксессуар" . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".($productParentCategory ? $productParentCategory->getToken() : '') . $outCsvDelimiter . $product->getId() . $outCsvDelimiter . $product->getBarcode() . $outCsvDelimiter . $product->getName() . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".($accessoryParentCategory ? $accessoryParentCategory->getToken() : '') . $outCsvDelimiter . $accessoryNotInJson->getBarcode() . $outCsvDelimiter . $accessoryNotInJson->getName() . $outCsvDelimiter . $isBuyable."\n");
                        }

                        // если у продукта не установлена родительская категория, то в формировании SEO-отчета
                        // этот продукт не участвует
                        if(!$productParentCategory) continue;

                        // получаем количество товаров в родительской категории товара
                        if(!isset($categoryProductsData[$productParentCategory->getToken()])) {
                            $categoryProductsData[$productParentCategory->getToken()]['count'] = 1;
                        } else {
                            $categoryProductsData[$productParentCategory->getToken()]['count']++;
                        }

                        // получаем количество товаров в родительской категории товара,
                        // к которым привязан хоть один аксессуар из категории в json
                        foreach ($jsonCategoryToken as $categoryToken) {
                            if(in_array($categoryToken, array_keys($accessoriesGrouped))) {
                                if(!isset($categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken])) {
                                    $categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken]['count'] = 1;
                                } else {
                                    $categoryProductsData[$productParentCategory->getToken()]['jsonCategories'][$categoryToken]['count']++;
                                }
                            }
                        }
                    }

                    $part++;
                }

                // формируем отчет для SEO
                foreach ($categoryProductsData as $parentCategoryToken => $parentCategoryData) {
                    if(!empty($parentCategoryData['jsonCategories'])) {
                        foreach ($parentCategoryData['jsonCategories'] as $accessoryCategoryToken => $accessoryCategoryData) {
                            fwrite($reportSeo, "http://www.enter.ru/catalog/".$rootCategory."/".$parentCategoryToken . $outCsvDelimiter . $parentCategoryData['count'] . $outCsvDelimiter . "http://www.enter.ru/catalog/".$rootCategory."/".$accessoryCategoryToken . $outCsvDelimiter . $accessoryCategoryData['count']."\n");
                        }
                    }
                }

                $dateEnd = new \DateTime();

$benchmark = fopen($benchmarkFilepath, 'a');
fwrite($benchmark, '['.date('Y-m-d H:i:s').']:'."\n");
fwrite($benchmark, 'timeGetAccessories: '.$timeGetAccessories.' сек.'."\n");
fwrite($benchmark, 'timeGetJson: '.$timeGetJson.' сек.'."\n");
fwrite($benchmark, 'timeExistingCategories: '.$timeExistingCategories.' сек.'."\n");
fwrite($benchmark, 'Общее время генерации отчетов : '. ($dateEnd->getTimestamp() - $dateStart->getTimestamp()) .' сек.'."\n");
fwrite($benchmark, "\n");
fclose($benchmark);

                fclose($reportBu);
                fclose($reportSeo);
            }
        }

        // удаляем лок-файл, чтобы при необходимости можно было перезапустить таск
        unlink($lockFilepath);
    }


}