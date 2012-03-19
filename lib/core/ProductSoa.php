<?php
class ProductSoa
{
    public $barcode;
    public $view_show;
    public $is_insale;
    public $is_instock;
    public $preview;
    public $price;
    public $view;
    public $kit;
    public $announce;
    public $cart_quantity;
    public $rating;



    public function __construct()
    {

    }


    protected $_mainPhoto = null;


    public static function generatePathByLink($link) {
       return str_replace('/product/', '', $link);
    }

    public function getPriceFormatted() {
        return number_format($this->price, 0, ',', ' ');
    }

    public static function priceFormat($price) {
        return number_format($price, 0, ',', ' ');
    }

    public function preDelete($event)
    {
        $invoker = $event->getInvoker();

        $this->deleteResultCache($invoker);

       // CacheEraser::getInstance()->log($this->getTable()->getCacheEraserKeys($invoker, 'delete'), 'product_ deleted');
    }

    public function preSave($event)
    {
        $invoker = $event->getInvoker();

        // If record has been modified adds keys to nginx file
//        if ($invoker->isModified(true) && ($invoker->getTable() instanceof myDoctrineTable))
//        {
//            CacheEraser::getInstance()->log($invoker->getTable()->getCacheEraserKeys($invoker, 'save'), 'product changed');
//        }

        $record = $event->getInvoker();

        if (empty($record->token))
        {
            $record->token = !empty($record->barcode) ? trim($record->barcode) : uniqid();
        }
    }

    public function postSave($event)
    {
        parent::postSave($event);

        $record = $event->getInvoker();

        if (array_key_exists('view_list', $record->getLastModified()))
        {
            //$region = $this->getTable()->getParameter('region');

            foreach ($record->CategoryRelation as $categoryRelation)
            {
                $this->getCache()->removeByTag("productCategory-{$categoryRelation['product_category_id']}/product-count".($region ? "/region-{$region['id']}" : ''));
            }
        }
    }

    public function __toString()
    {
        return (string) $this->name;
    }

    public function toParams()
    {
        return array(
            'product' => $this->link,
        );
    }


    public function getIsInsale()
    {
        //return $this->getTable()->isInsale($this);
        return true;
    }

    public function getParameterByProperty($property_id)
    {
        $return = null;
        foreach ($this->Parameter as $parameter)
        {
            if ($parameter->getProperty()->id != $property_id) continue;

            $return = $parameter;
        }

        return $return;
    }

    public function getRealPrice()
    {
        return $this->price;

    }

    public function getFormattedPrice()
    {
        return number_format($this->price, 0, ',', ' ');
    }

    public function getSimilarProduct(array $params = array())
    {
        return SimilarProductTable::getInstance()->getListByProduct($this, $params);
    }

    public function getCommentList(array $params = array())
    {
        return ProductCommentTable::getInstance()->getListByProduct($this, $params);
    }

    public function getCommentCount(array $params = array())
    {
        return $this->comments_num;
    }

    public function getUserTagList(array $params = array())
    {
        return UserTagTable::getInstance()->getListByProduct($this->id, $params);
    }

    public function getStockList(array $params = array())
    {
        return StockTable::getInstance()->getListByProduct($this->id, $params);
    }

    public function getShopList(array $params = array())
    {
        return ShopTable::getInstance()->getListByProduct($this->id, $params);
    }

    public function getServiceList(array $params = array())
    {
       // return ServiceTable::getInstance()->getListByProduct($this, $params);
    }

    public function getUsersRates()
    {
//        $data = UserProductRatingTable::getInstance()->getByProduct($this);
//        $result = array();
//        $maxPropertyValue = null;
//        $maxPropertyId = null;
//        foreach ($data as $row)
//        {
//            if (!isset($result[$row['property_id']]))
//            {
//                $result[$row['property_id']] = array(
//                    'value' => 0,
//                    'count' => 0,
//                    'name' => $row['Property']['name']
//                );
//            }
//            $result[$row['property_id']]['value'] += $row['value'];
//            $result[$row['property_id']]['count']++;
//        }
//        foreach ($result as $propId => &$prop)
//        {
//            if ($prop['value'] > $maxPropertyValue)
//            {
//                $maxPropertyValue = $prop['value'];
//                $maxPropertyId = $propId;
//            }
//            $prop['average'] = round($prop['value'] / $prop['count']);
//        }
//        $result['max_property_id'] = $maxPropertyId;
//
//        return $result;
    }

    public function getRatingStat()
    {
        $q = ProductCommentTable::getInstance()->createBaseQuery();
        $q->andWhere('product_id = ?', $this->id);
        $q->andWhere('parent_id = 0');
        $data = $q->fetchArray();
        $result = array(
            'count' => 0,
            'recomends' => 0,
            'percent' => 0,
            'rating_average' => 0,
            'rating_1' => 0,
            'rating_2' => 0,
            'rating_3' => 0,
            'rating_4' => 0,
            'rating_5' => 0,
        );
        $ratingSum = 0;
        if (count($data) > 0) {
            foreach ($data as $row) {
                $result['count']++;
                if ($row['is_recomend'] == 1) {
                    $result['recomends']++;
                }
                $ratingSum += $row['rating'];
                if ($row['rating'] > 0) {
                    $k = 'rating_'.$row['rating'];
                    $result[$k]++;
                }
            }
            $result['rating_average'] = round($ratingSum/count($data), 2);
            $result['percent'] = round(($result['recomends']/$result['count'])*100);
        }
        return $result;
    }


    public function getAllPhotos()
    {
       // return ProductPhotoTable::getInstance()->getByProduct($this);
        $urls = sfConfig::get('app_product_photo_url');
        $mediaList = $this->media;
        foreach ($mediaList as $k => & $media) {
            if ($media['type_id'] != 1) {
                unset($mediaList[$k]);
                continue;
            }
            //генерируем пути сразу для всех размеров
            foreach ($urls as $num => $url) {
                $media['path'][$num] = $url . $media['source'];
            }
        }
        return $mediaList;
    }

    public function getAll3dPhotos()
    {
        $d3List = array();
        $urls = sfConfig::get('app_product_photo_url');
        foreach ($this->media as $media) {
            if ($media['type_id'] == 2) {
                $media['path']['small'] =  $urls[0] . $media['source'];
                $media['path']['big'] =  $urls[1] . $media['source'];
                $d3List[] = $media;
            }
        }
        return $d3List;
    }

    public function getMainPhotoUrl($view = 0)
    {
       // return $this->getTable()->getMainPhotoUrl($this, $view);
        $urls = sfConfig::get('app_product_photo_url');
        return $this->media_image ? $urls[$view] . $this->media_image : null;
    }

    static public function getMainPhotoUrlByMediaImage($mediaImage, $view = 0)
    {
        // return $this->getTable()->getMainPhotoUrl($this, $view);
        $urls = sfConfig::get('app_product_photo_url');
        return $mediaImage ? $urls[$view] . $mediaImage : null;
    }

    //оставляем пока, как объект из бД!! (чтоб ничего не сломать)
    public function getMainCategory()
    {
        if (isset($this->category[0])) {
            $catId = $this->category[0]['id'];
            $cat = ProductCategoryTable::getInstance()->getById($catId);
            if ($cat && $cat->id) {
                return $cat;
            }
        }
        return null;
    }
    public function getMainCategoryId()
    {
        return isset($this->Category[0]) ? $this->Category[0]['id'] : null;
    }



    public function countParameter($view = null)
    {
        $count = 0;

        foreach ($this->Parameter as $productParameter)
        {
            // подсчитывает только свойства с определенным видом
            if ((null != $view) && !call_user_func(array($productParameter, 'isView'.ucfirst($view))))
            {
                continue;
            }

            $count++;
        }

        return $count;
    }


    public function isKit()
    {
        return (!is_null($this->kit));
    }

    public function getStockQuantity()
    {
        return StockProductRelationTable::getInstance()->getQuantityByProduct($this);
    }
}