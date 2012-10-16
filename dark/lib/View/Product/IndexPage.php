<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {

  /** @var string */
  protected $layout  = 'layout-default-oneColumn';

  public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }

  public function slotBodyDataAttribute() {
    return 'product_card';
  }

    public function slotMetaOg() {
        /** @var \Model\Product\Entity $product  */
        $product = $this->getParam('product');

        if (!$product) {
            return '';
        }

        if ($product->getDescription()) {
            $description = $product->getDescription();
        } elseif ($product->getTagline()) {
            $description = $product->getTagline();
        } else {
            $description = 'Enter - новый способ покупать. Любой из 20000 товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.';
        }

        return "<meta property=\"og:title\" content=\"".$product->getName()."\"/>\r\n".
                "<meta property=\"og:description\" content=\"".$description."\"/>\r\n".
                "<meta property=\"og:image\" content=\"".$product->getImageUrl(3)."\"/>\r\n".
                "<meta property=\"og:site_name\" content=\"ENTER\"/>\r\n".
                "<meta property=\"og:type\" content=\"website\"/>\r\n";
    }
}
