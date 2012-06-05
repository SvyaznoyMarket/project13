<h1><?php echo $news->name ?><br /></h1>

<div class="block">
  <?php include_partial('news/show', array('news' => $news)) ?>
</div>
