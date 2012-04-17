<?php //include_component('productCatalog', 'navigation_seo', array('product' => $product, 'productCategory' => $product->getMainCategory())) ?>

<?php slot('seo_counters_advance') ?>
  <?php //include_component('productCategory', 'seo_counters_advance', array('unitId' => $product->getMainCategory()->root_id)) ?>
  <script type="text/javascript">
    (function (d) {
      var HEIAS_PARAMS = [];
      HEIAS_PARAMS.push(['type', 'ppx'], ['ssl', 'auto'], ['n', '12564'], ['cus', '12675']);
      HEIAS_PARAMS.push(['pb', '1']);
      HEIAS_PARAMS.push(['product_id', '<?php echo $product->getBarcode() ?>']);
      if (typeof window.HEIAS === 'undefined') {
        window.HEIAS = [];
      }
      window.HEIAS.push(HEIAS_PARAMS);
      var scr = d.createElement('script');
      scr.async = true;
      scr.src = (d.location.protocol === 'https:' ? 'https:' : 'http:') + '//ads.heias.com/x/heias.async/p.min.js';
      var elem = d.getElementsByTagName('script')[0];
      elem.parentNode.insertBefore(scr, elem);
    }(document));
  </script>
<?php end_slot() ?>
