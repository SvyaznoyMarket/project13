<?php
slot('title',$serviceCategory['name'] );
slot('navigation');
  include_component('service', 'navigation', array('serviceCategory' => $serviceCategory));
end_slot();
?>


<?php 
if (!$serviceCategory['core_parent_id']){
   include_component('service', 'root_page', array('list' => $list));
} else { ?>    
    
    
    <div class="float100">
    <div class="column685 pr">
        <div class="line pb20 mt32"></div>
        <?php         
            include_component('service', 'current_category_tree', array('listInner' => $listInner, 'serviceList' => $serviceList, 'serviceCategory' => $serviceCategory));
        ?>                    
    </div>
    </div>

    <div class="column215">
    <?php 
        include_component('service', 'left_menu', array('list' => $list, 'serviceCategory' => $serviceCategory));
    ?>    
    </div>

<?php } ?>

