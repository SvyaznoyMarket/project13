<?php if (!empty($categories['product'])): ?>
<h2>Найдено в товарах:</h2>
<ul class="form checkboxlist pb15">
<?php $i = 0; foreach ($categories['product'] as $item): $i++ ?>
  <li><label for="checkbox-<?php echo $i ?>"><?php echo $item['record'] ?> <?php echo "({$item['count']})" ?></label><input id="checkbox-<?php echo $i ?>" name="checkbox-<?php echo $i ?>" type="checkbox" value="checkbox-<?php echo $i ?>" /></li>
<?php endforeach ?>
</ul>
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