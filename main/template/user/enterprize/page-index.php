<?php
/**
 * @var $page                              \View\User\OrdersPage
 * @var $helper                            \Helper\TemplateHelper
 * @var $user                              \Session\User
 * @var $orderCount                        int
 * @var $ordersByYear                      array
 * @var $orders                            \Model\User\Order\Entity[]
 * @var $orderProduct                      \Model\Order\Product\Entity|null
 * @var $product                           \Model\Product\Entity|null
 * @var $productsById                      \Model\Product\Entity[]
 * @var $point                             \Model\Point\PointEntity
 * @var $pointsByUi                        \Model\Point\PointEntity[]
 * @var $onlinePaymentAvailableByNumberErp bool[]
 * @var $viewedProducts                    \Model\Product\Entity[]
 */
?>

<div class="personal">
    <?= $page->render('user/_menu', ['page' => $page]) ?>

    Enterprize

</div>
