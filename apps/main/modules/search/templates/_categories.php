<?php if (count($list['product']) > 0): ?>
  <h2>Найдено в товарах:</h2>

  <form id="filter_product_type-form" action="<?php echo url_for('search') ?>" method="get">
    <input type="hidden" name="q" value="<?php echo $searchString ?>" />
    <?php include_component('product', 'filter_productType', array('productTypeList' => $list['product'])) ?>
  </form>
<?php endif ?>

<!--
<h2>Найдено в новостях:</h2>
<ul class="simplelist pb15">
  <li><a href="">Показать новости (3)</a></li>
</ul>
-->

<!--
<h2>Другие также искали:</h2>
<ul class="simplelist pb15">
  <li><a href="">Ожерелья</a></li>
  <li><a href="">Кольца</a></li>
  <li><a href="">Серьги</a></li>
</ul>
-->