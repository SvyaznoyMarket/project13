<?php

namespace EnterSite\Repository;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\Model;

class Compare {
    use ConfigTrait;

    /**
     * @param Http\Request $request
     * @return Model\Compare\Product|null
     */
    public function getProductObjectByHttpRequest(Http\Request $request) {
        $compareProduct = null;

        $productData = [
            'id' => null,
        ];
        if (!empty($request->query['product']['id'])) {
            $productData = array_merge($productData, $request->query['product']);
        } else if (!empty($request->data['product']['id'])) {
            $productData = array_merge($productData, $request->data['product']);
        }

        if ($productData['id']) {
            $compareProduct = new Model\Compare\Product();
            $compareProduct->id = (string)$productData['id'];
        }

        return $compareProduct;
    }

    /**
     * @param Http\Session $session
     * @return Model\Compare
     */
    public function getObjectByHttpSession(Http\Session $session) {
        $compare = new Model\Compare();

        $compareData = array_merge([
            'product' => [],
        ], (array)$session->get('compare'));

        foreach ($compareData['product'] as $productId => $productQuantity) {
            $compareProduct = new Model\Compare\Product();
            $compareProduct->id = (string)$productId;

            $compare->product[$compareProduct->id] = $compareProduct;
        }

        return $compare;
    }

    /**
     * @param Http\Session $session
     * @param Model\Compare $compare
     */
    public function saveObjectToHttpSession(Http\Session $session, Model\Compare $compare) {
        $compareData = [
            'product' => [],
        ];

        foreach ($compare->product as $compareProduct) {
            $compareData['product'][$compareProduct->id] = [
                'id' => $compareProduct->id,
            ];
        }

        $session->set('compare', $compareData);
    }

    /**
     * @param Model\Compare $compare
     * @param Model\Compare\Product $compareProduct
     */
    public function setProductForObject(Model\Compare $compare, Model\Compare\Product $compareProduct) {
        $compare->product[$compareProduct->id] = $compareProduct;
    }

    /**
     * @param Model\Compare $compare
     * @param Model\Compare\Product $compareProduct
     */
    public function deleteProductForObject(Model\Compare $compare, Model\Compare\Product $compareProduct) {
        if (array_key_exists($compareProduct->id, $compare->product)) {
            unset($compare->product[$compareProduct->id]);
        }
    }

    /**
     * @param $id
     * @param Model\Compare $compare
     * @return Model\Compare\Product|null
     */
    public function getProductById($id, Model\Compare $compare) {
        $return = null;

        foreach ($compare->product as $compareProduct) {
            if ($compareProduct->id === $id) {
                $return = $compareProduct;

                break;
            }
        }

        return $return;
    }

    /**
     * @param Model\Compare $compare
     * @param Model\Product[] $productsById
     */
    public function compareProductObjectList(Model\Compare $compare, array $productsById) {
        $compareFunction = function(Model\Product $product, Model\Product $productToCompare) {
            foreach ($product->properties as $property) {
                foreach ($productToCompare->properties as $propertyToCompare) {
                    if (!isset($property->equalProductIds)) {
                        $property->equalProductIds = [];
                    }

                    if ($property->id != $propertyToCompare->id) continue;

                    if ($property->value == $propertyToCompare->value) {
                        $property->equalProductIds[] = $productToCompare->id;
                    }
                }
            }
        };

        foreach ($compare->product as $comparedProduct) {
            /** @var Model\Product|null $product */
            $product = isset($productsById[$comparedProduct->id]) ? $productsById[$comparedProduct->id] : null;
            if (!$product) continue;

            // FIXME
            $product->compareGroupId = $product->category ? $product->category->id : null;

            foreach ($productsById as $productToCompare) {
                if ($product->id == $productToCompare->id) continue;
                $compareFunction($product, $productToCompare);
            }
        }
    }

    /**
     * @param Model\Compare $compare
     * @param Model\Product[] $productsById
     * @return Model\Compare\Group[]
     */
    public function getGroupListByObject(Model\Compare $compare, array $productsById) {
        $groupsById = [];

        foreach ($compare->product as $comparedProduct) {
            /** @var Model\Product|null $product */
            $product = isset($productsById[$comparedProduct->id]) ? $productsById[$comparedProduct->id] : null;
            if (!$product) continue;

            $groupId = $product->category ? $product->category->id : null;
            if (!$groupId || isset($groupsById[$groupId])) continue;

            $group = new Model\Compare\Group();
            $group->id = $groupId;
            $group->name = $product->category->name;

            $groupsById[$groupId] = $group;
        }

        return array_values($groupsById);
    }
}