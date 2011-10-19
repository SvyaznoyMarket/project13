<?php if (count($list['product']) > 0): ?>
  <h2>Найдено в товарах:</h2>
  <?php include_partial('search/filter_product', array('list' => $list['product'], 'url' => url_for('search', array('q' => $searchString)))) ?>
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