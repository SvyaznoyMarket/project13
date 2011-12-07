    <?php
    $usedList = array();
    #myDebug::dump($serviceList);
    foreach($listInner as $item){

        if ($item['level'] == ($serviceCategory['level']+1)){ ?>
            <?php 
               $currentServiceList = array(); 
               foreach($serviceList as $service){
                    foreach($service['ServiceCategoryRelation'] as $cat){
                        if ($cat['category_id'] == $item['id']){
                            $currentServiceList['list'][] = $service;
                            if (!isset($currentServiceList['photo']) && $service->getPhotoUrl(0)) {
                                $currentServiceList['photo'] = $service->getPhotoUrl(0);
                            }
                        }
                    }
               }
               if (count($currentServiceList['list'])<1) continue;
            ?>   
            <div class="serviceblock mNewSB">
                <div class="photo">
                    <?php if (isset($currentServiceList['photo'])) { ?>
                        <img width="160" height="120" alt="" src="<?php echo $currentServiceList['photo']; ?>">
                    <?php } else { ?>                        
                        <img alt="" src="/images/f1infobig.png">
                    <?php } ?>                        
                </div>
                <div class="info">
                    <h3><?php echo $item['name'] ?></h3>
                    <?php 
                    #print_r($currentServiceList);
                    foreach($currentServiceList['list'] as $service){ ?>
                        <?php $usedList[] = $service['id']; ?>
                        <div class="font16 pb8">
                            <a href="<?php echo url_for('service_show', array('service' => $service['token'])); ?>" >
                                <?php echo $service['name'] ?>
                            </a>    
                                <?php if ($service->getFormattedPrice()){ ?>
                                    &mdash; 
                                    <div class="font16 mInlineBlock">
                                        <strong>
                                            <?php echo $service->getFormattedPrice(); ?>
                                            <?php if((int)$service->getFormattedPrice()) { ?>
                                                &nbsp;<span class="rubl">p</span>
                                            <?php } ?>    
                                        </strong>
                                    </div>             
                                <?php }   ?>
                        </div>  
                        <div class="pb20">
                        <?php if (isset($service['description'])){ ?><?php echo $service['description'] ?><?php } ?>
                        <?php if (isset($service['work'])){ ?><?php echo $service['work'] ?> <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>                    
        <?php
        }
        /*
        } elseif ($item['level'] == ($serviceCategory['level']+2)){
            echo '<div><b>'.$item['name'].$item['id'].'</b></div>';
            foreach($serviceList as $service){
                foreach($service['ServiceCategoryRelation'] as $cat){
                    #echo $cat['category_id'] .'=='. $item['id'].'<br>';
                    if ($cat['category_id'] == $item['id']){
                        echo '<div>'.$service['name'].'</div>';
                    }
                }
            }
        }  */
         
         
    }
?>    
    
<?php 

/* сервисы, которые находятся прямо в категории второго уровня. Надо ли их отображать?
foreach($serviceList as $service){
    foreach($service['ServiceCategoryRelation'] as $cat){
        if ($cat['category_id'] == $serviceCategory['id'] && !in_array($service['id'], $usedList)){
            echo $service['name'] .'<br>';
        }
    }
}
 * */

?>