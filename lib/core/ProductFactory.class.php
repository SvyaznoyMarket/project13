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



    public function createProductFromCore($data, $loadFriendProducts = false)
    {

        if (!isset($data['id']) && !isset($data['slug'])) {
            return;
        }

        //загружаем данные о продукте
        $baseInfo = $this->_getDataFromCore($data, true);
        reset($baseInfo);
        $baseInfo = current($baseInfo);
       // print_r($baseInfo);
//        die();
        //создаём продукт с базывыми свойствами
        $product = $this->_setMainProps($baseInfo);


        //если требуется загрузка связанных продуктов, загружаем
        if ($loadFriendProducts) {
            $this->_loadFriendProducts($product, $baseInfo);
        }

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
                $service['priceFormatted'] = ProductSoa::priceFormat($service['price']);
                $serviceIdList[] = $service['id'];
            }
            //временно! получаем старые сайтвоый token услуг. Чтобы сгенерировать ссылки на них.
            //убрать после переделки раздела услуг!
            $siteServiceInfo = ServiceTable::getInstance()->getQueryObject()->whereIn('core_id', $serviceIdList)->fetchArray();
            foreach ($siteServiceInfo as $siteSevice) {
                foreach ($product->service as & $service) {
                    if ($siteSevice['core_id'] == $service['id']) {
                        $service['site_token'] = $siteSevice['token'];
                        break;
                    }
                }
            }
        }

        return $product;

//        if ($product->kit) {
//            $urls = sfConfig::get('app_product_photo_url');
//            foreach ($product->kit as & $kit) {
//                $kit['photo'] = $urls[2] . $kit['media_image'];
//                $kit['path'] = ProductSoa::generatePathByLink($kit['link']);
//                $kit['priceFormatted'] = ProductSoa::priceFormat($kit['price']);
//
//                $is_instock = $kit['state']['is_shop'] || $kit['state']['is_store'] || $kit['state']['is_supplier'];
//                if ($is_instock && $kit['price']>0) {
//                    $kit['is_insale'] = 1;
//                } else {
//                    $kit['is_insale'] = 0;
//                }
//
//            }
//        }

        return $product;
    }

    private function _loadFriendProducts($product, $baseInfo)
    {
        //собираем список ид продуктов, как либо связанных с текущем.
        $numRelatedOnPage = 5;
        $friendIdList = array_merge(
            array_slice($product->related, 0 , $numRelatedOnPage),
            array_slice($product->accessories, 0, $numRelatedOnPage)
        );
        //print_r($friendIdList);
        if (isset($product->model['product'])) {
            $product->model['product'][] = $product->id;
            $friendIdList = array_merge($friendIdList, $product->model['product']);
        }
        foreach ($product->kit as $kit) {
            $friendIdList[] = $kit['id'];
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
        foreach ($product->kit as $kit) {
            $kitList[] = $this->_setMainProps($friendsInfo[$kit['id']]);
        }
        $product->kit = $kitList;

        //для моделей
        if (isset($product->model['product'])) {
            $product->model['product'][] = $product->id;
            $modelList = array();
            foreach ($product->model['product'] as $model) {
                $modelList[] = $this->_setMainProps($friendsInfo[$model]);
            }
            $product->model['product'] = $modelList;
        }

        //для связанных продуктов
        $relatedList = array();
        foreach ($product->related as $related) {
            if (isset($friendsInfo[$related])) {
                $relatedList[] = $this->_setMainProps($friendsInfo[$related]);
            }
        }
        $product->related = $relatedList;

        //для аксессуаров
        $accessoriesList = array();
        foreach ($product->accessories as $accessory) {
            if (isset($friendsInfo[$accessory])) {
                $accessoriesList[] = $this->_setMainProps($friendsInfo[$accessory]);
            }
        }
        $product->accessories = $accessoriesList;
    }

    private function _setMainProps($info)
    {
        $product = new ProductSoa();

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
        return $product;
    }


    private function _getDataFromCore($data, $getDelivery = false)
    {
        $core = CoreSoa::getInstance();

        //загружаем статическе данные
        $productInfoStatic = $core->getProductStatic($data);
        if (isset($productInfoStatic['result'])) {
            throw new ErrorException('Товар не найден');
        }
        //var_dump($productInfoStatic);
        //die();

        //загружаем динамические данные
        $productInfoDynamic = $core->getProductDynamic($data);
        foreach ($productInfoStatic as $key => & $info) {
            $info = array_merge($productInfoDynamic[$key], $info);
        }

        //загружаем данные о доставках
        if ($getDelivery) {
            $productInfoDelivery = $core->getDeliveryCalc($data);
            foreach ($productInfoStatic as $key => & $info) {
                $info['delivery'] = $productInfoDelivery[$key];
            }
        } else {
            foreach ($productInfoStatic as $key => & $info) {
                $info['delivery1'] = array();
            }
        }

        return $productInfoStatic;
    }
}