<?php
namespace View\Partners;

class Admitad
{
    private $routeName;
    private $code;
    private $level;
    private $returnData;


    /**
     * @param $routeName  =  \App::request()->attributes->get('route')
     */
    public function __construct($routeName)
    {
        $this->routeName = $routeName;
        $this->code = \App::config()->partners['Admitad']['code'];
        $this->level = 0;
        //$this->returnData['routeName'] = $routeName;
    }



    /**
     * @param array $ad_data
     * @return $this->$returnData
     */
    public function toSend($ad_data = [])
    {
        if (!empty($ad_data)) $this->returnData['ad_data'] = $ad_data;
        $data = & $this->returnData['pushData'];

        $data['code'] = $this->code;
        $data['level'] = $this->level;

        return $this->returnData;
    }



    /**
     * @param \Model\Product\Category\Entity $category
     * @return $this->$returnData
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
     * @param \Model\Product\Entity $product
     * @return $this->$returnData
     */
    public function product($product)
    {
        $this->level = 2;
        $ad_data = [];

        if ($product instanceof \Model\Product\Entity) {
            $ad_data['ad_product'] = [
                'id' => $product->getId(),
                "vendor" => ( $product->getBrand() instanceof \Model\Brand\Entity ) ? $product->getBrand()->getName() : '',
                "price" => $product->getPrice(),
                "url" => \App::router()->generate('product', ['productPath' => $product->getPath()], true),
                "picture" => $product->getImageUrl(3),
                "name" => $product->getName(),
                "category" => $product->getMainCategory()->getId(),
            ];
        }

        return $this->toSend($ad_data);
    }



    /**
     * @param \Model\Cart\Product\Entity[] $cartProductsById
     * @return $this->$returnData
     */
    public function cart($cartProductsById)
    {
        $this->level = 3;
        $ad_data = [];
        $ad_data['ad_products'] = [];
        foreach ($cartProductsById as $cartProd) {
            if (! ($cartProd instanceof \Model\Cart\Product\Entity) ) continue;
            $ad_data['ad_products'][] = [
                'id' => $cartProd->getId(),
                'number' => $cartProd->getQuantity(),
            ];
        }

        return $this->toSend($ad_data);
    }



    /**
     * @param \Model\Order\Entity[] $orders
     * @return $this->$returnData
     */
    public function ordercomplete($orders)
    {
        $this->level = 4;
        $ad_data = [];

        $orderSum = 0;
        foreach ($orders as $order) {
            if (!($order instanceof \Model\Order\Entity)) continue;
            $orderSum += $order->getPaySum();
            $ad_data['ad_order'] = $order->getNumber();
            $ad_data['items'] = [];
            foreach ($order->getProduct() as $prod) {
                $ad_data['ad_products'][] = [
                    //'id' => $prod->getArticle(), // несущуствующий метод! // see comment to task SITE-1572
                    'id' => $prod->getId(),
                    'number' => $prod->getQuantity(),
                    //'price' => $prod->getPrice(),
                ];
            } // end of foreach (products)

        } // end of foreach ($orders)

        if ($orderSum) $ad_data['ad_amount'] = $orderSum;

        return $this->toSend($ad_data);
    }

}