<?php
namespace View;

class Sociomantic
{

    private $region_id = null;


    public function __construct($reg_id = null)
    {
        $this->region_id = $reg_id ? : \App::user()->getRegion()->getId();
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

        return $cart_prods;
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
        $id = false;
        //if ($product instanceof \Model\Product\Entity) {
        //$id = (string)$product->getId();
        /*
        if ( method_exists($product, 'getTypeId') and $product->getTypeId() ) {
            $id .= '-' . $product->getTypeId();
        }
        */

        if (method_exists($product, 'getArticle') and $product->getArticle()) {
            $id = $product->getArticle();
        }

        if ($this->region_id) $id .= '_' . $this->region_id;
        //}
        return $id;
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