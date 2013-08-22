<?php
namespace View\Partners;

class Admitad
{
    private $routeName;
    private $code;
    private $level;
    private $returnData;


    public function __construct($routeName)
    {
        $this->routeName = $routeName;
        $this->code = \App::config()->partners['Admitad']['code'];
        $this->level = 0;
        //$this->returnData['routeName'] = $routeName;
    }


    /**
     * @param array $ad_data
     * @return mixed
     */
    public function toSend($ad_data = [])
    {
        if (!empty($ad_data)) $this->returnData['ad_data'] = $ad_data;
        $data = & $this->returnData['pushData'];

        $data['code'] = $this->code;
        $data['level'] = $this->level;

        /*print '<pre>';
        print_r($this->returnData); // for debug
        print '</pre>';*/
        return $this->returnData;
    }



    /**
     * @param $category
     * @return mixed
     */
    public function category($category) {
        $this->level = 1;
        $ad_data = [];

        if ($category instanceof \Model\Product\Category\Entity) {
            $ad_data['ad_category'] = $category->getId();
        }        
        return $this->toSend($ad_data);
    }



    /**
     * @param $product
     * @return mixed
     */
    public function product($product)
    {
        $this->level = 2;
        $ad_data = [];

        if ($product instanceof \Model\Product\Entity) {
            print '</pre>';
            print_r($product->getPath());
            print '</pre>';
            $ad_data['ad_product'] = [
                'id' => $product->getId(),
                "vendor" => $product->getBrand()->getName(),
                "price" => $product->getPrice(),
                "url" => $product->getPath(),
                "picture" => $product->getImageUrl(3),
                "name" => $product->getName(),
                "category" => $product->getMainCategory()->getId(),
            ];
        }

        return $this->toSend($ad_data);
    }



    /**
     * @param $cartProductsById
     * @return mixed
     */
    public function cart($cartProductsById)
    {
        $this->level = 3;
        $ad_data = [];
        if ($cartProductsById instanceof \Model\Cart\Product\Entity) {
            $ad_data['ad_products'] = [
                'id' => $cartProductsById->getId(),
                'number' => $cartProductsById->getQuantity(),
            ];
        }
        return $this->toSend($ad_data);
    }



    /**
     * @param \Model\Order\Entity[] $orders
     * @return mixed
     */
    public function ordercomplete($orders)
    {
        $this->level = 4;
        $ad_data = [];

        if ( is_array($orders) and !empty($orders) ) {
            $orderSum = 0;
            foreach ($orders as $order) {

                $orderSum += $order->getPaySum();
                if ( !($order instanceof \Model\Order\Entity) ) {
                    continue;
                }
                $ad_data['ad_order'] = $order->getNumber();
                $ad_data['items'] = [];
                foreach ($order->getProduct() as $prod) {
                    $ad_data['items'][] = [
                        //'id' => $prod->getArticle(), // несущуствующий метод! // see comment to task SITE-1572
                        'id' => $prod->getId(),
                        'number' => $prod->getQuantity(),
                        //'price' => $prod->getPrice(),
                    ];
                } // end of foreach (products)

            } // end of foreach ($orders)

            if ($orderSum) $ad_data['ad_amount'] = $orderSum;

        }

        return $this->toSend($ad_data);
    }

}