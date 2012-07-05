<?php
namespace light;

/**
 * Created by JetBrains PhpStorm.
 * User: Kuznetsov
 * Date: 19.06.12
 * Time: 13:19
 * To change this template use File | Settings | File Templates.
 */
interface CartPriceContainer
{

  /**
   * @abstract
   * @param CartContainer $container
   * @return array
   */
  public function getPrices(CartContainer $container);

}
