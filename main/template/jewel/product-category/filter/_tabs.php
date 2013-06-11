<?php

use Model\Product\Filter\Entity as FilterEntity;

/**
 * @var $page          \View\Layout
 * @var $category      \Model\Product\Category\Entity
 * @var $productFilter \Model\Product\Filter
 * @var $filter        \Model\Product\Filter\Entity
 * @var $isOpened      bool
 * @var $index         int
 * @var $formName      string
 */
?>

<?php $values = $productFilter->getValue($filter) ?>

<ul class="brand-subnav__list">
    <li><a <?= empty($values) ? 'class="active"' : '' ?> href="<?= $category->getLink()?>?scrollTo=<?= $scrollTo ?>">Все</a></li>
    <? foreach ($filter->getOption() as $option) { $id = $option->getId() ?>
        <li><a <?= in_array($id, $values) ? 'class="active"' : '' ?> href="<?= $category->getLink()?>?f<?= urlencode('[' . strtolower($filter->getId()) . '][]' ) . '=' . $id ?>&scrollTo=<?= $scrollTo ?>"><?= $option->getName() ?></a></li>
    <? } ?>
</ul>

<script type="text/javascript">
  $(document).ready(function(){
    handle_small_tabs()
    handle_jewel_filters_pagination()

    function handle_small_tabs() {
      $('.brand-subnav__list a').click(function(event){
        $('.brand-subnav__list a').removeClass('active')
        $(this).addClass('active')
        $('#ajaxgoods_top').show()
        $.get($(this).attr('href'),{},function(data){
          $('.filter-section').html(data.filters)
          $('#pagerWrapper').html(data.pager)
          handle_jewel_filters_pagination()
          handle_custom_items()
        }).done(function(){
          $('#ajaxgoods_top').hide()
        })
        event.stopPropagation()
        return false
      })
    }

    function handle_jewel_filters_pagination() {
      $('.filter-section a, .pageslist a, .allpager a').click(function(event){
        $('#ajaxgoods_top').show()
        $.get($(this).attr('href'),{},function(data){
          $('.filter-section').html(data.filters)
          $('#pagerWrapper').html(data.pager)
          handle_jewel_filters_pagination()
          handle_custom_items()
        }).done(function(){
          $('#ajaxgoods_top').hide()
        })
        event.stopPropagation()
        return false
      })
    }
    
  })
</script>