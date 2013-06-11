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
    handle_jewel_filters()

    function handle_small_tabs() {
      $('.brand-subnav__list a').click(function(event){
        $('.brand-subnav__list a').removeClass('active')
        $(this).addClass('active')
        $('#ajaxgoods_top').show()
        $.get($(this).attr('href'),{},function(data){
          $('.items-section__list').html(data.items)
          $('.filter-section').html(data.filters)
          handle_jewel_filters()
          handle_custom_items()
        }).done(function(){
          $('#ajaxgoods_top').hide()
        })
        event.stopPropagation()
        return false
      })
    }

    function handle_jewel_filters() {
      $('.filter-section a').click(function(event){
        $('#ajaxgoods_top').show()
        $.get($(this).attr('href'),{},function(data){
          $('.items-section__list').html(data.items)
          $('.filter-section').html(data.filters)
          handle_jewel_filters()
          handle_custom_items()
          console.log('>>>>>>')
          console.log(data.items)
          console.log('>>>>>>')
        }).done(function(){
          $('#ajaxgoods_top').hide()
        })
        event.stopPropagation()
        return false
      })
    }
  })
</script>