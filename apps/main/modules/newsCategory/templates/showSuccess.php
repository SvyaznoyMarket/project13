<h1><?php echo $newsCategory ?></h1>

<div class="left">
  <div class="block">
    <?php include_component('news', 'pagination', array('newsPager' => $newsPager)) ?>
  </div>
  <div class="block">
    <?php include_component('news', 'pager', array('newsPager' => $newsPager)) ?>
  </div>
  <div class="block">
    <?php include_component('news', 'pagination', array('newsPager' => $newsPager)) ?>
  </div>
</div>
<div  class="block left">
  <?php include_component('news', 'filter'); ?>
</div>
