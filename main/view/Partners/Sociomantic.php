<?php
namespace View\Partners;

class Sociomantic
{

    private $region_id = null;
    public $sess;


    public function __construct($reg_id = null)
    {
        $this->region_id = $reg_id ? : \App::user()->getRegion()->getId();
        $this->sess = \App::session();
    }


    public function makeCategories($breadcrumbs, $category = null, $ctype = 'product')
    {
        $prod_cats = [];
        $useLastItem = false; // $ctype == 'product'
        if ($ctype == 'category') $useLastItem = true;

        if ($breadcrumbs and is_array($breadcrumbs)) {
            $prod_cats = $this->breadcrumbsToArray($breadcrumbs, $useLastItem);
        } else {
            //if (is_array($category)) $category = reset($category);
            if ($category) {
                $prod_cats[] = $category->getName();
            }
        }

        return $prod_cats;
    }


    /**
     * @param \Model\Product\Entity[] $products
     * @param \Model\Cart\Product\Entity[] $cartProductsById
     * @return array
     */
    public function makeCartProducts($products, $cartProductsById)
    {
        $cart_prods = [];

        if ($products && is_array($products)) {
            foreach ($products as $product) {
                $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;
                if (!$cartProduct) continue;
                $one_prod = [];

                $one_prod['identifier'] = $this->resetProductId($product);

                $one_prod['quantity'] = $cartProduct->quantity;
                //$one_prod['amount'] = $this->helper->formatPrice( $cartProduct->getPrice() * $one_prod['quantity'] );
                $one_prod['amount'] = $cartProduct->price * $one_prod['quantity'];
                $one_prod['currency'] = 'RUB';

                $cart_prods[] = $one_prod;
                if (!$cartProduct) continue;
            }
        }

        $this->makeSession( $products );

        return $cart_prods;
    }

    /**
     * @param \Model\Product\Entity[] $products
     */
    public function makeSession( $products ) {
        $ids_arr = null;

        if ($products && is_array($products)) {
            foreach ($products as $product) {
                $id = $this->resetProductId($product);
                $ids_arr[$product->getId()] = $id;
            }
        }

        if ( is_array($ids_arr) and !empty($ids_arr)  ) return $this->setInSession( $ids_arr );
        return false;
    }



    public function getFromSession() {
        $ids_arr = $this->sess->get('ids_arr');
        return $ids_arr;
    }

    public function setInSession( &$ids_arr ) {
        if ( isset($ids_arr) ) $this->sess->set( 'ids_arr', ($ids_arr) );
    }

    public function restoreSession() {
        return $this->sess->remove('ids_arr');
    }



    public function wrapEscapeQuotes($value) {
        if ( is_numeric($value) ) return $value;
        $val = trim( (string)$value );

        $first = substr($value, 0, 1);
        if ( $first == '{' || $first == '[' ) return $val;

        $val = str_replace("'", '"', $val);
        $val = "'".$val."'";

        return $val;
    }

    /**
     * @param $product \Model\Product\Entity
     * @return bool|string
     */
    public function resetProductId($product)
    {
        $article = null;
        $ids_arr = $this->getFromSession();

        if (   is_array($ids_arr)   and   !empty($ids_arr)   and   isset( $ids_arr[$product->getId()] )   ) {
            return $ids_arr[$product->getId()];
        }

        if (method_exists($product, 'getArticle') and $product->getArticle()) {
            $article = $product->getArticle();
        }else{
            $article = 0;
        }

        $id = (string) $article;

        if ($this->region_id) $id .= '_' . $this->region_id;

        return $id ?: 0;
    }


    /**
     * Конвертирует хлебные крошки в массив
     * @param $breadcrumbs
     * @return bool|string
     */
    private function breadcrumbsToArray( $breadcrumbs, $useLastItem = false )
    {
        if ( !empty($breadcrumbs) && is_array($breadcrumbs) ) {

            $count = count($breadcrumbs);
            $arr = [];
            $i = 0;

            foreach ($breadcrumbs as $item) {
                $i++;
                if ( ( !$useLastItem && $i < $count) || ($useLastItem || $i==1) ) {
                    $arr[] = $item['name'];
                }
            }

            return $arr;
        }

        return false;
    }


    public function makeProdInfo(\Model\Product\Entity $product, $prod_cats) {
        $prod = [];
        $domain = $_SERVER['HTTP_HOST'] ? : $_SERVER['SERVER_NAME'];
        //$region_id = \App::user()->getRegion()->getId();
        $brand = $product->getBrand() ? $product->getBrand()->getName() : null;
        $photo = $product->getMainImageUrl('product_500');

        $prod['identifier'] = $this->resetProductId($product);
        $prod['fn'] = $product->getName();
        $prod['category'] = $prod_cats;

        $prod['description'] = $product->getTagline();
        if (empty($prod['description'])) {
            $prod['description'] = $product->getDescription();
            if (empty($prod['description'])) {
                $prod['description'] = $product->getName();
            } elseif (strlen($prod['description']) > 90) {
                $prod['description'] = substr($prod['description'], 0, 90) . '...';
            }

            if ( empty($prod['description']) ) {
                $prod['description'] = $product->getPrefix();
            }
        }

        $prod['currency'] = 'RUB';
        $prod['url'] = 'http://' . $domain . strtok($_SERVER["REQUEST_URI"], '?');
        $prod['price'] = $product->getPrice(); //стоимость со скидкой
        $prod['amount'] = $product->getPriceOld(); // стоимость без скидки
        if (!$prod['amount']) $prod['amount'] = $prod['price'];
        if ($photo) $prod['photo'] = $photo;
        if ($brand) $prod['brand'] = $brand;

        $brandUis = [
            '381b2197-e510-11e0-83b4-005056af265b', // Hasbro
            '4e76b2a9-a674-11e1-ae16-3c4a92f6ffb8', // BeyBlades Hasbro
            '4e76b2af-a674-11e1-ae16-3c4a92f6ffb8', // Starwars Hasbro
            '78249c00-dcab-11e1-9bf4-3c4a92f6ffb8', // Hasbro Amazing Spider-man
            'd8d416db-f374-11e2-93ed-e4115baba630', // Furby Famosa
            'dde56e05-986c-11e2-bb85-e4115b118798', // Furby
            '3d3fe23c-e3a9-11e2-9e3c-e4115b118798', // Transformers Prime
            '4e76b2b0-a674-11e1-ae16-3c4a92f6ffb8', // Transformers Hasbro
            '5a144e7c-ae3b-11e1-be71-3c4a92f6ffb8', // Transformers
            'f71fe7c0-9a99-11e2-bb85-e4115b118798', // Nerf
            '93ecd13a-28bf-11e2-bb99-3c4a92f6ffb8', // My Little Pony
            'ac878d09-5843-11e3-93ee-e4115baba630', // FurReal Friends
            '93ecd13b-28bf-11e2-bb99-3c4a92f6ffb8', // Littlest Pet Shop
            '6b218ed2-2259-11e2-bb99-3c4a92f6ffb8', // Angry Birds
            '0eb7b007-e51e-11e0-83b4-005056af265b', // Marvel
            '82b714df-eac8-11e2-ac9e-e4115b118798', // Marvel Characters
            'd86972fc-adfc-11e1-be71-3c4a92f6ffb8', // Star Wars
            '730f7400-1c0d-11e2-bb99-3c4a92f6ffb8', // KRE-O
            '93ecd139-28bf-11e2-bb99-3c4a92f6ffb8', // Play-Doh
            '61beddf1-e928-11e2-ac9e-e4115b118798', // Playskool
            '88bc4c6b-8e24-11e3-93ee-e4115baba630', // BABY ALIVE
        ];

        if ($product->getPrice() < 500 && (!$product->getBrand() || !in_array($product->getBrand()->getUi(), $brandUis))) {
            $valid = time(); // SITE-4258
        } else {
            $valid = $product->getIsBuyable() ? 0 : time(); // Если товара нет в наличии, то необходимо передавать отметку времени
        }

        $prod['valid'] = $valid;

        return $prod;
    }

}