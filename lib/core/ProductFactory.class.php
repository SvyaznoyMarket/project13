<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olga
 * Date: 11.03.12
 * Time: 12:19
 * To change this template use File | Settings | File Templates.
 */


class ProductFactory
{

    const LABEL_SALE = 1;

    const MIN_SERVICE_BUY_PRICE = 950;

    /**
     * Количество связанных продуктов и аксессуаров, которые загружаем вместе с продуктом сразу
     * @var int
     */
    public $numRelatedOnPage = 5;

    public $numAccessoriesOnPage = 5;

    /**
     * Загружает список продуктов из ядра
     *
     * @param $data  - данные о продуктах, которые  нужно загрузить - списки id и/или slug
     * @param bool $loadFriendProducts - загружать ли "дрижественные" продукты (комплекты, модели, связанные. аксессуары)
     * @param bool $loadDelivery - загрузать ли информацию о доставках
     * @return array - массив объектов ProductSoa
     */
    public function createProductFromCore($data, $loadFriendProducts = false, $loadDelivery = false, $loadTmpData = false)
    {

        if (!isset($data['id']) && !isset($data['slug'])) {
            return;
        }

        //загружаем данные о продукте
        $baseInfoArray = $this->_getDataFromCore($data, $loadDelivery);

        $result = array();
        //для каждого из загруженных продуктов
        foreach ($baseInfoArray as $baseInfo) {

            //создаём продукт с базывыми свойствами
            $product = $this->_createBaseProduct($baseInfo);

            //если требуется загрузка связанных продуктов, загружаем
            if ($loadFriendProducts) {
                $this->_loadFriendProducts($product, $baseInfo);
            }
            if ($loadTmpData) {
                //некоторая информацию из БД пока требуется
                $this->_loadTmpDbData($product);
            }
            $result[] = $product;
        }

        return $result;
    }

    private function _loadTmpDbData($product)
    {
        //дополнительные данные
        if ($product->tag) {
            $tagIdList = array();
            foreach ($product->tag as & $tag) {
                $tagIdList[] = $tag['id'];
            }
            //временно! получаем старые сайтвоый token тегов. Чтобы сгенерировать ссылки на них.
            //убрать после переделки раздела тегов!
            $siteTagInfo = TagTable::getInstance()->getQueryObject()->whereIn('core_id', $tagIdList)->fetchArray();
            foreach ($siteTagInfo as $siteTag) {
                foreach ($product->tag as & $tag) {
                    if ($siteTag['core_id'] == $tag['id']) {
                        $tag['site_token'] = $siteTag['token'];
                        break;
                    }
                }
            }
        }

        if ($product->service) {
            $serviceIdList = array();
            foreach ($product->service as & $service) {
                if ($service['price'] < 1) {
                    $service['priceFormatted'] = 'бесплатно';
                } else {
                    $service['priceFormatted'] = ProductSoa::priceFormat($service['price']);
                }
                $serviceIdList[] = $service['id'];
            }
            //временно! получаем старые сайтвоый token услуг. Чтобы сгенерировать ссылки на них.
            //убрать после переделки раздела услуг!
            $siteServiceInfo = ServiceTable::getInstance()->getQueryObject()->whereIn('core_id', $serviceIdList)->fetchArray();
            foreach ($siteServiceInfo as $siteSevice) {
                foreach ($product->service as & $service) {
                    if ($siteSevice['core_id'] == $service['id']) {
                        $service['site_token'] = $siteSevice['token'];
                        $service['only_inshop'] = $siteSevice['only_inshop'];
                        if (!$service['only_inshop'] && $service['price'] && $service['price'] >= self::MIN_SERVICE_BUY_PRICE) {
                            $service['in_sale'] = true;
                        } else {
                            $service['in_sale'] = false;
                        }
                        break;
                    }
                }
            }
        }
//        print_r($product->service);
//        die();

    }

    private function _loadFriendProducts($product, $baseInfo)
    {
        //собираем список ид продуктов, как либо связанных с текущем.
        $friendIdList = array_merge(
            array_slice($product->related, 0 , $this->numRelatedOnPage * 2),
            array_slice($product->accessories, 0, $this->numRelatedOnPage * 2)
        );
        //print_r($friendIdList);
        if (isset($product->model['product'])) {
            $product->model['product'][] = $product->id;
            $friendIdList = array_merge($friendIdList, $product->model['product']);
        }
        if (count($product->kit)) {
            foreach ($product->kit as $kit) {
                $friendIdList[] = $kit['id'];
            }
        }
//        print_r($friendIdList);
//        die();
        if (!count($friendIdList)) {
            return;
        }
        //обращаемся к ядру за информацией
        $friendsInfo = $this->_getDataFromCore( array('id' => $friendIdList) );
        // print_r($friendsInfo);
        //создаём объекты для првязанных продуктов::

        //для комплектов
        $kitList = array();
        if (count($product->kit)) {
            foreach ($product->kit as $kit) {
                $kitList[] = $this->_createBaseProduct($friendsInfo[$kit['id']]);
            }
            $product->kit = $kitList;
        }

        //для моделей
        if (isset($product->model['product'])) {
            $product->model['product'][] = $product->id;
            $modelList = array();
            foreach ($product->model['product'] as $model) {
                $modelList[] = $this->_createBaseProduct($friendsInfo[$model]);
            }
            $product->model['product'] = $modelList;
        }

        //для связанных продуктов
        $relatedList = array();
        if (count($product->related)) {
            foreach ($product->related as $related) {
                if (isset($friendsInfo[$related])) {
                    $relatedList[] = $this->_createBaseProduct($friendsInfo[$related]);
                }
            }
        }
        $product->related = $relatedList;

        //для аксессуаров
        $accessoriesList = array();
        if (count($product->accessories)) {
            foreach ($product->accessories as $accessory) {
                if (isset($friendsInfo[$accessory])) {
                    $accessoriesList[] = $this->_createBaseProduct($friendsInfo[$accessory]);
                }
            }
        }
        $product->accessories = $accessoriesList;
    }

    /**
     * Из массива создаёт объект ProductSoa и заполняет базовые свойства. (не требующие выполнения каких-либо дополнительных запросов)
     *
     * @param $info
     * @return ProductSoa
     */
    private function _createBaseProduct($info)
    {
        $product = new ProductSoa();
        if (!$info['id']) {
            return;
        }

        foreach ($info as $fieldName => $value) {
            $product->$fieldName = $value;
        }
        if (count($product->kit)) {
            $product->view = 'kit';
        }
        $product->preview = $product->announce;
        $product->barcode = $product->bar_code;
        if (!$product->rating) {
           $product->rating = 0;
        }
        //$product->priceFormatted = ProductSoa::priceFormat($kit['price']);

        //$this->token_prefix = '';

        $product->path = ProductSoa::generatePathByLink($product->link);


        //есть на складе, если есть хоть где-нибудь
        $product->is_instock = $product->state['is_shop'] || $product->state['is_store'] || $product->state['is_supplier'];

        if ($product->is_instock && $product->price>0) {
            $product->is_insale = 1;
        } else {
            $product->is_insale = 0;
        }

        $product->fullKitList = $product->kit;
        $product->fullRelatedList = $product->related;
        $product->fullAccessoriesList = $product->accessories;

        foreach ($product->category as & $cat) {
            if (!isset($cat['is_shown_in_menu'])) {
               $cat['is_shown_in_menu'] = 1;
            }
        }

        $product->sale_label = false;
        if ($product->label) {
            foreach ($product->label as $label) {
                if ($label['id'] == self::LABEL_SALE) {
                    $product->sale_label = true;
                }
            }
        }
        //print_r($product);
        return $product;
    }


    /**
     *  Получает информацию о продуктах из ядра
     *
     * @param $data - инфа о продуктах
     * @param bool $getDelivery - нужна ли информация о доставках
     * @return array
     */
    private function _getDataFromCore($data, $getDelivery = false)
    {
        $core = CoreSoa::getInstance();

        $core->resetData();
        $core->prepareDataForStatic($data);
        $region = sfContext::getInstance()->getUser()->getRegion();
        $data['geo_id'] = $region['core_id'];
        $core->prepareDataForDynamic($data);
        if ($getDelivery) {
            //доставки через новый API пока не загружаем!!! 1С к этому не готов
            //$core->prepareDataForDelivery($data);
        }
        $core->multiThreadQuery();
        $coreResult = $core->getData();
//        print_r($coreResult);
//        die();

        $productsBaseData = array();
        foreach ($coreResult as $queryResult) {
           if (!isset($queryResult['result']) || !isset($queryResult['result']['result'])) {
               continue;
           }
           foreach ($queryResult['result']['result'] as $itemKey => $itemData) {
                if ($queryResult['action'] == 'product/get-delivery') {
                    $productsBaseData[$itemKey]['delivery'] = $itemData;
                } elseif (!isset($productsBaseData[$itemKey])) {
                    $productsBaseData[$itemKey] = $itemData;
                } elseif (is_array($productsBaseData[$itemKey]) && is_array($itemData)) {
                    $productsBaseData[$itemKey] = array_merge($productsBaseData[$itemKey], $itemData);
                }
           }
        }
        if (!count($productsBaseData)) {
            throw new ErrorException('Товар не найден');
        }
//        print_r($productsBaseData);
//        die();
        return $productsBaseData;

    }
}