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


    public function makeCategories($breadcrumbs, $category = null)
    {
        $prod_cats = '';
        if ($breadcrumbs and is_array($breadcrumbs)) {
            $prod_cats = $this->breadcrumbsToString($breadcrumbs);
        } else {
            if ($category) $prod_cats = $category->getName();
        }
        return $prod_cats;
    }


    public function makeCartProducts($products, $cartProductsById)
    {
        $cart_prods = [];

        if ( !empty($products) and is_array($products) )
        foreach ($products as $product):
            $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;
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
        $val = (string)$value;
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
     * TODO: проверить. похоже этот кривоватый метод уже не нужен
     * Возвращает категории продукта в виде строки (для js-скрипта например) исходя из масива
     * @param $prod_cats_arr
     * @return string|bool
     */
    public function prod_cats_in_string($prod_cats_arr = null, $prod_cats_names = null)
    {
        if (empty($prod_cats_arr) and empty($prod_cats_names)) return false;

        if (is_array($prod_cats_arr))
            foreach ($prod_cats_arr as $item) {
                if ($item instanceof \Model\Product\Category\Entity) {
                    $categories_names_arr = $item->getName();
                }
            }

        if (!empty($prod_cats_names)) {

            if (is_string($prod_cats_names)) {
                $categories_names_arr[] = $prod_cats_names;
            } else

                if (is_array($prod_cats_names)) {
                    foreach ($prod_cats_names as $item) {
                        $categories_names_arr[] = $item;
                    }
                }
        }


        $count = count($categories_names_arr);
        if ($count < 1) return false;

        $i = 0;
        $prod_cats_string = "[";

        foreach ($categories_names_arr as $catName) {
            if (is_string($catName)) {
                $i++;
                $catName = str_replace('"', "'", $catName);
                $prod_cats_string .= " '" . $catName . "'";
                if ($i < $count) $prod_cats_string .= ", ";
            } else {
                $count--;
            }
        }

        $prod_cats_string .= " ]";
        return $prod_cats_string;
    }


    /**
     * Конвертирует хлебные крошки в строку
     * @param $breadcrumbs
     * @return bool|string
     */
    private function breadcrumbsToString($breadcrumbs)
    {
        foreach ($breadcrumbs as $item) {
            $str = $item['name'];
            if ($str) {
                $str = str_replace("'", '"', $str);
                $str = "'" . $str . "'";
            }
            $arr[] = $str;
        }

        $str = implode(', ', $arr);
        if ($str) {
            $str = '[' . $str . ']';
            return $str;
        }

        return false;
    }

}