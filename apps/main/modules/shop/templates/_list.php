<!-- bMapInfo -->
<div class='bMapShops__eInfo'>
  <h2 class='bMapShops__eTitle'>Все магазины Enter в <?php echo (($region['type'] == 'city')? 'г.':'').$region['name'] ?></h2>

  <?php foreach ($shopList as $shop): ?>
    <!-- __eCard -->
    <?php include_component('shop', 'show', array('view' => 'inlist', 'shop' => $shop)) ?>
    <!-- /__eCard -->
  <?php endforeach ?>

</div>
<!-- /bMapInfo -->

