<?php

/**
 * news actions.
 *
 * @package    enter
 * @subpackage news
 * @author     Связной Маркет
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class newsActions extends myActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeShow(sfWebRequest $request)
  {
    $this->news = $this->getRoute()->getObject();
  }

    public function executeIpad(sfWebRequest $request)
    {
        $this->getResponse()->setTitle('The new iPad');
        $productAr = array(
            'planshetniy-kompyuter-apple-new-ipad-16-gb-cherniy-2060101004336',
            'planshetniy-kompyuter-apple-new-ipad-32-gb-cherniy-2060101004343',
            'planshetniy-kompyuter-apple-new-ipad-4g-16-gb-cherniy-2060101004350',
            'planshetniy-kompyuter-apple-new-ipad-4g-32-gb-cherniy-2060101004367',
            'planshetniy-kompyuter-apple-new-ipad-4g-64-gb-cherniy-2060101004374',
            'planshetniy-kompyuter-apple-new-ipad-64-gb-cherniy-2060101004381',
            'planshetniy-kompyuter-apple-new-ipad-16-gb-beliy-2060101004398',
            'planshetniy-kompyuter-apple-new-ipad-32-gb-beliy-2060101004404',
            'planshetniy-kompyuter-apple-new-ipad-64-gb-beliy-2060101004411',
            'planshetniy-kompyuter-apple-new-ipad-4g-16-gb-beliy-2060101004428',
            'planshetniy-kompyuter-apple-new-ipad-4g-32-gb-beliy-2060101004435',
            'planshetniy-kompyuter-apple-new-ipad-4g-64-gb-beliy-2060101004442',
        );
        $productAr = array('sushilka-dlya-belya-eurogold-smart-2040201001961');

        $factory = new ProductFactory();
        $productObList = $factory->createProductFromCore(array('slug' => end($productAr)), true, true, true);
        //print_r($productObList);
        $result1 = array();
        $result2 = array();
        $num = 0;
        foreach ($productObList as $product) {
            if ($num<=6) {
                $result1[] = array(
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->getMainPhotoUrl(1),
                );
            } else {
                $result2[] = array(
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->getMainPhotoUrl(1),
                );
            }
            $num++;
        }
        //print_r($result);
        $this->setVar('list1', $result1);
        $this->setVar('list2', $result2);
        //echo 'ipad';
        //die();
        //   $this->news = $this->getRoute()->getObject();
    }
}
