<?php
// добавить в кронтаб выполнение комманды раз в сутки
// php ./console.php Command/TagJsonAction generate local

namespace Controller\Command;

class TagJsonAction {
    public function __construct() {
        if ('cli' !== PHP_SAPI) {
            throw new \Exception('Действие доступно только через CLI');
        }
    }


    /**
     * Создает теги на основе json-файлов, присваивает тэги товарам, обновляет seo-json-файлы категорий
     *
     */
    public static function generate($max_parts = 0) {
        ini_set("auto_detect_line_endings", true);
        $sourceJsonDir = \App::config()->cmsDir . '/v1/seo/tag';
        $categoryJsonDir = \App::config()->cmsDir . '/v1/seo/catalog';
        if(!is_dir($sourceJsonDir) || !is_dir($categoryJsonDir)){
            throw new \Exception(sprintf('TagJson: не найден один или оба каталога %s, %s', $sourceJsonDir, $categoryJsonDir));
        }
        $client = \App::coreClientV2();
        $categoryTags = [];

        foreach (scandir($sourceJsonDir) as $file) {
            if(preg_match('/^(.+)\.json$/', $file, $matches)) {
                $dateStart = new \DateTime();

                $sourceJsonFilePath = $sourceJsonDir . '/' . $file;

                // парсим json с входными данными по тэгу
                $tagJson = null;
                if (($sourceJson = file_get_contents($sourceJsonFilePath)) !== FALSE) {
                    $tagJson = json_decode($sourceJson, true);
                }
                
                // пробуем создать новый тэг
                $response = self::createTags([$tagJson['name']]);

                // если тэг существует, значит имя файла уже является его токеном
                if(is_object($response) && (get_class($response) == 'Curl\Exception') && $response->getCode() == 1003) {
                    $tagToken = $matches[1];
                }
                // если тэг создался, получаем его токен
                elseif(is_array($response) && !empty($response[0]['token'])) {
                    $tagToken = $response[0]['token'];
                    rename($sourceJsonDir.'/'.$file, $sourceJsonDir.'/'.$tagToken.'.json');
                }
                // иначе - произошла ошибка и токен неизвестен, переходим к следующему файлу
                else {
                    continue;
                }

                /*
                 * тэгируем товары, подпадающие под заданные в json фильтры
                 */
                ;
                if(!empty($tagJson['filter']) && preg_match("/\/([^\\/]*)\?/", $tagJson['filter'], $categoryTokenMatches) && ($category = \RepositoryManager::productCategory()->getEntityByToken($categoryTokenMatches[1]))) {

                    /*
                     * подготавливаем фильтры и получаем список товаров, соответствующий этим фильтрам
                     */
                    $filtersTmp = explode('&', urldecode(preg_replace('/^.*\?/', '', $tagJson['filter'])));
                    $filterValues = [];
                    foreach ($filtersTmp as $key => $filterTmp) {
                        if(preg_match('/\[([^\]]*)\](\[([^\]]*)\])?=(.*)/', $filterTmp, $matches)) {
                            if(empty($matches[3])) {
                                $filterValues[$matches[1]][] = $matches[4];
                            } else {
                                $filterValues[$matches[1]][$matches[3]] = $matches[4];
                            }
                        }
                    }

                    $filtersCollection = [];
                    foreach ($filterValues as $filterId => $filterValue) {
                        $filter = new \Model\Product\Filter\Entity();
                        $filter->setId($filterId);
                        if (array_key_exists('from', $filterValue)) $filter->setMin($filterValue['from']);
                        if (array_key_exists('to', $filterValue)) $filter->setMax($filterValue['to']);
                        array_push($filtersCollection, $filter);
                    }

                    $productFilter = new \Model\Product\Filter($filtersCollection);
                    $productFilter->setCategory($category);
                    $productFilter->setValues($filterValues);

                    // получаем товары по частям, чтобы в случае большой выборки снизить нагрузку на сервер
                    $part = 1;
                    $step = 100;
                    $count = \RepositoryManager::product()->countByFilter(
                        $productFilter->dump()
                    );

                    // если по текущим фильтрам товаров не найдено, переходим к следующему файлу
                    if(empty($count)) continue;

                    while ($part <= (int)ceil($count / $step)) {
                        if($max_parts && $part > $max_parts) break;

                        $productIds = \RepositoryManager::product()->getIdsByFilter(
                            $productFilter->dump(),
                            [],
                            ($part - 1) * $step,
                            $step
                        );

                        // массово устанавливаем товарам тэги
                        $response = self::tagProducts($tagToken, $productIds);

                        // TODO: сейчас ответ после тэгирования никак не обрабатывается, так как результат ни на что не влияет
                        // в будущем возможно будет иметь смысл как-то его обрабатываеть

                        $part++;
                    }

                }

                /*
                 * получаем категории, протэгированные текущим тэгом
                 */
                if($tag = \RepositoryManager::tag()->getEntityByToken($tagToken)) {
                    $tagCategories = $tag->getCategory();
                }

                /*
                 * получаем категории продуктов на основе категорий тэгов, чтобы получить токены
                 */
                $productCategories = \RepositoryManager::productCategory()->getCollectionById(array_map(function($cat){return $cat->getId();}, $tagCategories));

                /*
                 * изменяем json файлы для категорий
                 */
                foreach ($productCategories as $category) {
                    $seoJsonFilepath = $categoryJsonDir . '/' . $category->getToken() . '.json';
                    if(is_file($seoJsonFilepath)) {
                        $seoJson = json_decode(file_get_contents($seoJsonFilepath));
                        $newHotLink = new \StdClass();
                        $newHotLink->title = $tagJson['name'];
                        $newHotLink->url = '/tags/' . $tagToken;
                        foreach ($seoJson as $accessLevel) {
                            if(isset($accessLevel->hotlinks) && !in_array($newHotLink, $accessLevel->hotlinks)) {
                                array_push($accessLevel->hotlinks, $newHotLink);
                            } elseif(!isset($accessLevel->hotlinks)) {
                                $accessLevel->hotlinks = [$newHotLink];
                            }
                        }

                        if(!file_put_contents($seoJsonFilepath, str_replace('\/', '/', json_encode($seoJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)))) {
                            throw new \Exception(sprintf('TagJson: не удалось записать данные в %s', $seoJsonFilepath));
                        }
                    }
                }
            }
        }
    }


    /**
     * создает новые тэги.
     */
    private static function createTags(array $tagNames) {
        $client = \App::coreClientPrivate();
        $response = [];
        $client->addQuery('tag/create', [], [
                'tag_name_list' => $tagNames,
                'http_user' => \App::config()->corePrivate['user'],
                'http_password' => \App::config()->corePrivate['password'],
            ], function($data) use(&$response) {
                $response = $data;
            },  function($data) use(&$response) {
                $response = $data;
        });
        $client->execute(\App::config()->corePrivate['retryTimeout']['medium']);
        return $response;
    }


    /**
     * массово тэгирует товары.
     */
    private static function tagProducts($token, array $productIds) {
        $client = \App::coreClientPrivate();
        $response = [];
        $client->addQuery('tag/set-product', [], [
                'token' => $token,
                'product_id_list' => $productIds,
                'http_user' => \App::config()->corePrivate['user'],
                'http_password' => \App::config()->corePrivate['password'],
            ], function($data) use(&$response) {
                $response = $data;
            },  function($data) use(&$response) {
                $response = $data;
        });
        $client->execute(\App::config()->corePrivate['retryTimeout']['medium']);
        return $response;
    }


}
