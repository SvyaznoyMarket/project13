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
     * @return array    $returnData
     */
    public function transaction($orders)
    {
        $data = & $this->returnData['sendData'];
        $data = [];

        foreach ($orders as $order) {
            if (!$order instanceof \Model\Order\Entity) {
                continue;
            }

            $items = [];
            foreach ($order->getProduct() as $prod) {
                $items[] = [
                    //'id' => $prod->getArticle(), // несущeствующий метод! // see comment to task SITE-1572
                    'id' => $prod->getId(),
                    'qnt' => $prod->getQuantity(),
                    'price' => $prod->getPrice(),
                ];
            }

            $data[] = [
                'transaction' => $order->getNumber(),
                'items' => $items,
            ];
        }

        return $this->returnData;
    }


    /**
     * @param \Model\Product\Entity|null    $product
     * @return array    $returnData
     */
    public function product($product)
    {
        $data = & $this->returnData['sendData'];

        if ($product instanceof \Model\Product\Entity) {
            $data = [$product->getId()];
        }

        return $this->returnData;
    }


    /**
     * @param \Model\Product\Category\Entity|null   $category
     * @return array    $returnData
     */
    public function category($category)
    {
        $data = & $this->returnData['sendData'];

        if ($category instanceof \Model\Product\Category\Entity) {
            $data = [$category->getId()];
        }

        return $this->returnData;
    }


}