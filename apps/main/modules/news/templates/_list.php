<?php if (!isset($list[0])): ?>
  <p>нет новостей</p>

<?php else: ?>
<ul>
  <?php foreach ($list as $item): ?>
    <li>
      <strong><?php echo link_to($item['name'], 'news_show', array('sf_subject' => $item['news'], 'year' => date('Y', strtotime($item['news']->published_at)), 'month' => date('m', strtotime($item['news']->published_at)), 'newsCategory' => $item['news']->Category->token, )) ?></strong>
      <?php //include_component('product', 'property', array('product' => $item['product'])) ?>
    </li>
  <?php endforeach ?>
</ul>
<?php endif ?>