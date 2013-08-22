<?php
namespace View\Partners;

class RetailRocket
{
    private $routeName;
    private $returnData;


    public function __construct($routeName)
    {
        $this->routeName = $routeName;
        $this->returnData['routeName'] = $routeName;
    }


    /**
     * @param \Model\Order\Entity[] $orders
     * @return mixed
     */
    public function transaction($orders)
    {
        $data = & $this->returnData['sendData'];

        //$orderSum = 0;
        foreach ($orders as $order) {

            //$orderSum += $order->getPaySum();
            if (!$order instanceof \Model\Order\Entity) {
                continue;
            }
            $data['transaction'] = $order->getNumber(); // count($orders) по идеее не может быть > 1.
            // transaction — свойство $order и по смыслу у всего $orders должно быть одно
            foreach ($order->getProduct() as $prod) {
                $data['items'][] = [
                    //'id' => $prod->getArticle(), // несущуствующий метод! // see comment to task SITE-1572
                    'id' => $prod->getId(),
                    'qnt' => $prod->getQuantity(),
                    'price' => $prod->getPrice(),
                ];
            } // end of foreach (products)

        } // end of foreach ($orders)

        return $this->returnData;
    }


    /**
     * @param \Model\Product\Entity $product
     * @return mixed
     */
    public function product(\Model\Product\Entity $product)
    {
        $data = & $this->returnData['sendData'];

        if ($product instanceof \Model\Product\Entity) {
            $data = $product->getId();
        }

        return $this->returnData;
    }


}