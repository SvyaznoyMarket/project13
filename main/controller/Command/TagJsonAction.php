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
        $tagJsonDir = \App::config()->cmsDir . '/v1/seo/tag';
        $categoryJsonDir = \App::config()->cmsDir . '/v1/seo/catalog';
        if(!is_dir($tagJsonDir) || !is_dir($categoryJsonDir)){
            throw new \Exception(sprintf('TagJson: не найден один или оба каталога %s, %s', $tagJsonDir, $categoryJsonDir));
        }
        $client = \App::coreClientV2();
        $categoryTags = [];

        foreach (scandir($tagJsonDir) as $file) {
            if(preg_match('/^(.+)\.json$/', $file, $matches)) {
                $tagJsonFilePath = $tagJsonDir . '/' . $file;

                // парсим json с входными данными по тэгу
                $tagJson = null;
                if (($tagJson = file_get_contents($tagJsonFilePath)) !== FALSE) {
                    $tagJson = json_decode($tagJson, true);
                }

                if(!$tagJson) {
                    echo sprintf('TagJson: не удалось загрузить json из файла %s', $tagJsonFilePath)."\n";
                    continue;
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
                    rename($tagJsonDir.'/'.$file, $tagJsonDir.'/'.$tagToken.'.json');
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
                        if (array_key_exists('from', $filterValue)) {
                            $filter->setMin($filterValue['from']);
                            $filter->setTypeId(\Model\Product\Filter\Entity::TYPE_SLIDER);
                        }
                        if (array_key_exists('to', $filterValue)) {
                            $filter->setMax($filterValue['to']);
                            $filter->setTypeId(\Model\Product\Filter\Entity::TYPE_SLIDER);
                        }
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

                    $productIds = [];

                    while ($part <= (int)ceil($count / $step)) {
                        if($max_parts && $part > $max_parts) break;

                        $productIdsPart = \RepositoryManager::product()->getIdsByFilter(
                            $productFilter->dump(),
                            [],
                            ($part - 1) * $step,
                            $step
                        );

                        $productIds = array_merge($productIds, $productIdsPart);

                        $part++;
                    }

                    // массово устанавливаем товарам тэги
                    $response = self::tagProducts($tagToken, $productIds);
                    // TODO: сейчас ответ после тэгирования никак не обрабатывается,
                    // так как результат ни на что не влияет
                    // в будущем возможно будет иметь смысл как-то его обрабатываеть
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
                    if(!is_file($seoJsonFilepath)) {
                        $newSeoJson = new \StdClass();
                        $newSeoJson->private = new \StdClass();
                        $newSeoJson->public = new \StdClass();
                        if(!file_put_contents($seoJsonFilepath, str_replace('\/', '/', json_encode($newSeoJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)))) {
                            throw new \Exception(sprintf('TagJson: не удалось записать данные в %s', $seoJsonFilepath));
                        }
                    }
                    $seoJson = json_decode(file_get_contents($seoJsonFilepath));
                    $newHotLink = new \StdClass();
                    $newHotLink->title = $tagJson['name'];
                    $newHotLink->url = '/tags/' . $tagToken;
                    $newHotLink->active = 1;
                    foreach ($seoJson as $accessLevel) {
                        if(isset($accessLevel->autohotlinks) && !in_array($newHotLink, $accessLevel->autohotlinks)) {
                            array_push($accessLevel->autohotlinks, $newHotLink);
                        } elseif(!isset($accessLevel->autohotlinks)) {
                            $accessLevel->autohotlinks = [$newHotLink];
                        }
                    }

                    if(!file_put_contents($seoJsonFilepath, str_replace('\/', '/', json_encode($seoJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)))) {
                        throw new \Exception(sprintf('TagJson: не удалось записать данные в %s', $seoJsonFilepath));
                    }
                }
            }
        }

        // удаляем тэги из json-фалов категорий, для которых не найден json-файл тэга
        self::removeHotlinksForNonExistingTags($tagJsonDir, $categoryJsonDir);
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


    /**
     * удаляет тэги из json-фалов категорий, для которых не найден json-файл тэга
     */
    private static function removeHotlinksForNonExistingTags($tagJsonDir, $categoryJsonDir) {
        foreach (scandir($categoryJsonDir) as $file) {
            if(preg_match('/^(.+)\.json$/', $file, $matches)) {
                $categoryJsonFilePath = $categoryJsonDir . '/' . $file;

                // парсим json с входными данными по категории
                $categoryJson = null;
                if (($categoryJson = file_get_contents($categoryJsonFilePath)) !== FALSE) {
                    $categoryJson = json_decode($categoryJson, true);
                }

                if(!$categoryJson) {
                    echo sprintf('TagJson: не удалось загрузить json из файла %s', $categoryJsonFilePath)."\n";
                    continue;
                }

                if(empty($categoryJson['public']['hotlinks']) && 
                   empty($categoryJson['private']['hotlinks']) && 
                   empty($categoryJson['public']['autohotlinks']) && 
                   empty($categoryJson['private']['autohotlinks'])) {
                    continue;
                }

                foreach ($categoryJson as $accessLevelKey => $accessLevel) {
                    foreach (['hotlinks', 'autohotlinks'] as $typeKey => $type) {
                        if(isset($accessLevel[$type])) {
                            foreach ($accessLevel[$type] as $hotlinkKey => $hotlink) {
                                if(isset($hotlink['url']) && preg_match('/([^\/]+)$/', $hotlink['url'], $matches) && !file_exists($tagJsonDir . '/' . $matches[1] . '.json')) {

                                    unset($categoryJson[$accessLevelKey][$type][$hotlinkKey]);

                                    if(!file_put_contents($categoryJsonFilePath, str_replace('\/', '/', json_encode($categoryJson, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)))) {
                                        throw new \Exception(sprintf('removeHotlinksForNonExistingTags: не удалось записать данные в %s', $categoryJsonFilePath));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


}
