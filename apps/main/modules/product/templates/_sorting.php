<span class="left">сортировать &nbsp; </span>
<ul class="inline sorting">
<?php foreach ($list as $item): ?>
  <li><?php echo link_to($item['title'].' '.('asc' == $item['direction'] ? '&darr;' : '&uarr;'), $item['url'], array('class' => $active['name'] == $item['name'] ? 'active' : '')) ?></li>
<?php endforeach ?>
</ul>