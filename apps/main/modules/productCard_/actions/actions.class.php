<?php

/**
 * productCard actions.
 *
 * @package    enter
 * @subpackage productCard_
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @method sfWebResponse getResponse
 */
class productCard_Actions extends myActions
{
  CONST NUM_RELATED_ON_PAGE = 5;

  public function preExecute()
  {
    parent::postExecute();

    $this->getRequest()->setParameter('_template', 'product_card');
  }

  public function executeIndex(sfWebRequest $request)
  {
    $a = explode('/', $request['product']);
    $productToken = end($a);
    $this->loadProduct($productToken);
  }

  public function executeShowByBarcode(sfWebRequest $request)
  {
    $this->getResponse()->setStatusCode(404);
    /** @var $siteProduct Product */
    $siteProduct = ProductTable::getInstance()->getByBarcode($request['product']);
    $this->loadProduct($siteProduct->token);
    $this->setTemplate('index');
  }

  public function executeAccessory(sfWebRequest $request)
  {
    $page = $request->getParameter('page', 1);
    $token = $request->getParameter('product');
    $product = RepositoryManager::getProduct()->getByToken($token, true);
    $this->forward404If(!$product);

    $begin = self::NUM_RELATED_ON_PAGE * ($page - 1);
    $accessoryIdList = array_slice($product->getAccessoryIdList(), $begin, self::NUM_RELATED_ON_PAGE);
    $accessoryProductList = RepositoryManager::getProduct()->getListById($accessoryIdList, true);

    foreach ($accessoryProductList as $i => $accessory)
      $this->renderPartial('product_/show_', array(
        'view' => 'extra_compact',
        'item' => $accessory,
        'maxPerPage' => self::NUM_RELATED_ON_PAGE,
        'ii' => $i
    ));
    return sfView::NONE;
  }

  public function executeRelated(sfWebRequest $request)
  {
    $page = $request->getParameter('page', 1);
    $token = $request->getParameter('product');
    $product = RepositoryManager::getProduct()->getByToken($token, true);
    $this->forward404If(!$product);

    $begin = self::NUM_RELATED_ON_PAGE * ($page - 1);
    $relatedIdList = array_slice($product->getRelatedList(), $begin, self::NUM_RELATED_ON_PAGE);
    $relatedProductList = RepositoryManager::getProduct()->getListById($relatedIdList, true);

    foreach ($relatedProductList as $i => $accessory)
      $this->renderPartial('product_/show_', array(
        'view' => 'extra_compact',
        'item' => $accessory,
        'maxPerPage' => self::NUM_RELATED_ON_PAGE,
        'ii' => $i
    ));
    return sfView::NONE;
  }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария
     *
     * @param $product
     * @return array
     */
  private function _getDataForCredit($product) {
      $result = array();
      $mainCat = $product->getCategoryList();
      $mainCat = $mainCat[0];
      $cart = $this->getUser()->getCart();
      $productType = CreditBankRepository::getCreditTypeByCategoryToken($mainCat->getToken());
      $dataForCredit = array(
          'price' => $product->getPrice(),
          'articul' => $product->getArticle(),
          'name' => $product->getName(),
          'count' => $product->getCartQuantity(),
          'product_type' => $productType,
          'session_id' => session_id()
      );
      $result['creditIsAllowed'] = (bool) (($product->getPrice() * (($product->getCartQuantity() > 0)? $product->getCartQuantity() : 1)) > ProductEntity::MIN_CREDIT_PRICE );
      $result['creditData'] = json_encode($dataForCredit);
      return $result;
  }

  private function loadProduct($productToken)
  {
    $product = RepositoryManager::getProduct()->getByToken($productToken, true);
    $this->forward404If(!$product);
    $this->getContext()->set('adriverProductInfo', array('productId' => $product->getId(), 'categoryId' => 0));
    RepositoryManager::getProduct()->loadRelatedAndAccessories($product, true, self::NUM_RELATED_ON_PAGE * 2);
    RepositoryManager::getProduct()->loadKit($product, true);
    $this->forward404If(!$product);

    $this->getResponse()->setTitle(sprintf(
      '%s - купить по цене %s руб. в Москве, %s - характеристиками и описанием и фото от интернет-магазина Enter.ru',
      $product->getName(),
      $product->getPrice(),
      $product->getName()
    ));
    $this->getResponse()->addMeta('description', sprintf(
      'Интернет магазин Enter.ru предлагает купить: %s по цене %s руб. На нашем сайте Вы найдете подробное описание и характеристики товара %s с фото. Заказать понравившийся товар с доставкой по Москве можно у нас на сайте или по телефону 8 (800) 700-00-09.',
      $product->getName(),
      $product->getPrice(),
      $product->getName()
    ));
    $this->getResponse()->addMeta('keywords', sprintf('%s Москва интернет магазин купить куплю заказать продажа цены', $product->getName()));

    if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
        $showRelatedUpper = false;
    } else {
        $showRelatedUpper = true;
    }

    $dataForCredit = $this->_getDataForCredit($product);

    $this->setVar('dataForCredit', $dataForCredit);
    $this->setVar('showRelatedUpper', $showRelatedUpper);
    $this->setVar('showAccessoryUpper', !$showRelatedUpper);
    $this->setVar('relatedPagesNum', ceil(count($product->getRelatedIdList()) / self::NUM_RELATED_ON_PAGE));
    $this->setVar('accessoryPagesNum', ceil(count($product->getAccessoryIdList()) / self::NUM_RELATED_ON_PAGE));
    $this->setVar('product', $product);
    $this->setVar('view', 'compact');
  }
}
