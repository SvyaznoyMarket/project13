<ul class="product_<?php echo $product->id ?>_comment-block">
<?php foreach ($list as $item): ?>
  <li style="margin-left: <?php echo ($item['level'] * 40) ?>px">
    <strong><?php echo $item['date'] ?></strong> от <?php echo $item['author'] ?>

    <ul class="inline">
      <li><a href="<?php echo $item['answer_url'] ?>">ответить</a></li>
      <li><a href="<?php echo url_for(array('sf_route' => 'productComment_helpful', 'sf_subject' => $item['productComment'], 'product' => $product->token, 'helpful' => 'yes')) ?>">полезный</a> (<?php echo $item['helpful'] ?>)</li>
      <li><a href="<?php echo url_for(array('sf_route' => 'productComment_helpful', 'sf_subject' => $item['productComment'], 'product' => $product->token, 'helpful' => 'no')) ?>">бесполезный</a> (<?php echo $item['unhelpful'] ?>)</li>
    </ul>

    <?php echo $item['content'] ?>
  </li>
<?php endforeach ?>
</ul>
