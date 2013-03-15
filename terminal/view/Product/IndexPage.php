<?php

namespace Terminal\View\Product;

class IndexPage extends \Terminal\View\DefaultLayout {
	public function prepare() {
		/** @var $product \Model\Product\Entity */
        $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
        if (!$product) {
            return;
        }

        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];

            foreach ($product->getCategory() as $category) {
                $breadcrumbs[] = array(
                    'name' => $category->getName(),
                    'screenType'  => $category->getHasChild() ? 'category' : 'product_listing',
                    'categoryId' => $category->getId(),
                    'hasLine' = > $category->getHasLine(),
                );
            }

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

	}

    public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }
}
