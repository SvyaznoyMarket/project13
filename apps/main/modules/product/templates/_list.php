<?php if (!isset($list[0])){ ?>
  <p>нет товаров</p>

<?php
}else{
    if ($ajax_flag){
        include_partial('product/list_ajax_'.$view, $sf_data);
    } else {
        include_partial('product/list_'.$view, $sf_data);        
    }
   
}
?>