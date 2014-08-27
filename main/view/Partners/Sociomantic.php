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


    public function makeCartProducts($products, $cartProductsById)
    {
        $cart_prods = [];

        if ( !empty($products) and is_array($products) )
        foreach ($products as $product):
            $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;
            if (!$cartProduct) continue;
            $one_prod = [];

            // $product <--> Model\Product\CartEntity
            $one_prod['identifier'] = $this->resetProductId($product);

            $one_prod['quantity'] = $cartProduct->getQuantity();
            //$one_prod['amount'] = $this->helper->formatPrice( $cartProduct->getPrice() * $one_prod['quantity'] );
            $one_prod['amount'] = $cartProduct->getPrice() * $one_prod['quantity'];
            $one_prod['currency'] = 'RUB';

            $cart_prods[] = $one_prod;
            if (!$cartProduct) continue;
        endforeach;

        $this->makeSession( $products );

        return $cart_prods;
    }


    public function makeSession( $products ) {
        $ids_arr = null;

        if ( is_array($products) and !empty($products) )
        foreach ($products as $product):
            // $product <--> Model\Product\CartEntity
            $id = $this->resetProductId($product);
            $ids_arr[ $product->getId() ] = $id;
        endforeach;

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
        $photo = $product->getImageUrl(3);

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

        $valid = $product->getIsBuyable() ? 0 : time(); // Если товара нет в наличии, то необходимо передавать отметку времени
        // SITE-4258
        if ((bool)$product->getCategory()) {
            $productCategories = array_filter(array_map(function($category){
                return $category instanceof \Model\Product\Category\Entity ? $category->getName() : null;
            }, $product->getCategory()));

            $categoryFilter = ['Tchibo', 'Игрушки Hasbro'];
            $filteredCategories = array_filter($productCategories, function($category) use ($categoryFilter) {
                return in_array($category, $categoryFilter) ? true : false;
            });

            if ($product->getPrice() < 500 && !(bool)$filteredCategories) {
                $valid = time();
            }
        }

        $prod['valid'] = $valid;

        return $prod;
    }

}