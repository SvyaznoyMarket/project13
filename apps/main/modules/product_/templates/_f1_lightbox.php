<?php
/**
 * @var $item ProductEntity
 */
$list = $item->getServiceList();
$listInCart = $item->getServiceListInCart()->getRawValue(); // symfony code sheet!
?>
<div class="hideblock bF1Block mGoods" style="display: none;">
  <i class="close" title="Закрыть">Закрыть</i>
  <h2>Добавление услуги F1</h2>
  <?php
  if (count($list) > 3) echo '<div>';
  ?>
  <table>
    <tbody>
    <?php foreach ($list as $service):?>
    <tr>

      <td class="bF1Block_eInfo"><?php echo $service->getName() ?><br>
        <a href="<?php echo url_for('service_show', array('service'=>$service->getToken())) ?>">Подробнее об услуге</a>
      </td>
      <td class="bF1Block_eBuy" ref="<?php echo $service->getToken() ?>">
        <?php if ($service->getPrice()) { ?>
          <span class="bF1Block_ePrice">
            <?php echo formatPrice($service->getPrice()) ?>&nbsp;<span class="rubl">p</span>
          </span>
        <?php } ?>
        <?php if ($service->getOnlyInShop()) { ?>
          <span class='bF1Block__eInShop'>доступна в магазине</span>
        <?php } elseif ($service->isInSale() && in_array($service, $listInCart)) { ?>
        <input data-f1title="<?php echo $service->getName() ?>" data-f1price="<?php echo $service->getPrice() ?>" data-fid="<?php echo $service->getId();?>"
               data-url="<?php echo url_for('cart_service_add', array('service'=>$service->getId(), 'product' => $item->getId())) ?>"
               ref="<?php echo addslashes($service->getToken());?>"
               type="button" class="active button yellowbutton" value="В корзине">
        <?php } elseif ($service->isInSale()) { ?>
        <input data-f1title="<?php echo $service->getName() ?>" data-f1price="<?php echo $service->getPrice() ?>" data-fid="<?php echo $service->getId();?>"
               data-url="<?php echo url_for('cart_service_add', array('service'=>$service->getId(), 'product' => $item->getId())) ?>"
               ref="<?php echo addslashes($service->getToken());?>"
               type="button" class="button yellowbutton" value="Купить услугу">
        <?php } ?>
      </td>

    </tr>
      <?php  endforeach ?>
    <tr>
      <th colspan="2"><a href="<?php echo url_for('service_list') ?>">Подробнее о Сервисе F1</a></th>
    </tr>
    </tbody></table>
  <?php if (count($list) > 3) echo '</div>';
  ?>
</div>