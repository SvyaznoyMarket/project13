<?
$user = $userForm;
$productsCounts = [];
foreach ($order->getProduct() as $key => $product) {
  $productsCounts[$product->getId()] = $product->getQuantity();
}
$products = \RepositoryManager::product()->getCollectionById(array_map(function($product){
  return $product->getId();
}, $order->getProduct()));
?>

<script type="text/javascript">
  //<![CDATA[
    var _flocktory = window._flocktory = _flocktory || [];
    _flocktory.push({
      "order_id":     "<?= $order->getId() ?>",
      "email":        "<?= $emailOrPhone = $user->getEmail() ? $user->getEmail() : $user->getMobilePhone().'@mail.ru' ?>",
      "name":         "<?= implode(' ', [$user->getFirstName(), $user->getLastName()]) ?>",
      "sex":          "<?= $user->getFirstName() && preg_match('/[аяa]$/', $user->getFirstName()) ? 'f' : 'm' ?>",
      "price":        <?= $order->getProductSum() ?>,
      "custom_field": "<?= $order->getNumber() ?>",
      "items": [
        <? foreach ($products as $key => $product) { ?>
          {
            "id":    "<?= $product->getArticle() ?>",
            "title": "<?= $product->getName() ?>",
            "price":  <?= $product->getPrice() ?>,
            "image": "<?= $product->getImageUrl() ?>",
            "count":  <?= $productsCounts[$product->getId()] ?>
          }
          <?= $key < count($products) - 1 ? ',' : '' ?>
        <? } ?>
      ]
    });

    (function() {
      var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true;
      s.src = "//api.flocktory.com/1/hello.js";
      var l = document.getElementsByTagName('script')[0]; l.parentNode.insertBefore(s, l);
    })();
  //]]>
</script>
