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


    public function toSend($ad_data = [])
    {
        if (!empty($ad_data)) $this->returnData['ad_data'] = $ad_data;
        $data = & $this->returnData['pushData'];

        $data['code'] = $this->code;
        $data['level'] = $this->level;

        /*print '<pre>';
        print_r($this->returnData);
        print '</pre>';*/
        return $this->returnData;
    }



    public function category($category) {
        $this->level = 1;
        $ad_data = [];
        if ($category instanceof \Model\Product\Category\Entity) {
            $ad_data = [
                'ad_category' => $category->getId()
            ];   
        }        
        return $this->toSend($ad_data);
    }


    public function product($product)
    {

        if ($product instanceof \Model\Product\Entity) {
            $data = $product->getId();
        }

        $this->level = 2;

        return $this->toSend();
    }


    public function cart()
    {
        $this->level = 3;
        return $this->toSend();
    }


    /**
     * @param \Model\Order\Entity[] $orders
     * @return mixed
     */
    public function ordercomplete($orders)
    {
        $data = & $this->returnData['pushData'];
        $this->level = 4;

        //$orderSum = 0;
        foreach ($orders as $order) {

            //$orderSum += $order->getPaySum();
            if (!$order instanceof \Model\Order\Entity) {
                continue;
            }
            $data['ad_order'] = $order->getNumber(); // count($orders) по идеее не может быть > 1.
            // transaction — свойство $order и по смыслу у всего $orders должно быть одно
            foreach ($order->getProduct() as $prod) {
                $data['items'][] = [
                    //'id' => $prod->getArticle(), // несущуствующий метод! // see comment to task SITE-1572
                    'id' => $prod->getId(),
                    'number' => $prod->getQuantity(),
                    //'price' => $prod->getPrice(),
                ];
            } // end of foreach (products)

        } // end of foreach ($orders)

        return $this->toSend();
    }



}