    <?php
    #print_r($serviceList);
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
            <div class="serviceblock">
                <?php if (isset($currentServiceList['photo'])) { ?>
                    <div class="photo">
                        <img width="160" height="120" alt="" src="<?php echo $currentServiceList['photo']; ?>">
                    </div>
                <?php } ?>
                <div class="info">
                    <h3><?php echo $item['name'] ?></h3>
                    <?php 
                    #print_r($currentServiceList);
                    foreach($currentServiceList['list'] as $service){ ?>
                        <div class="font16 pb8">
                            <a href="<?php echo url_for('service_show', array('service' => $service['token'])); ?>" >
                                <?php echo $service['name'] ?>
                            </a>    
                        </div>  
                        <?php if (isset($service['description'])){ ?><div class="pb5"><?php echo $service['description'] ?> </div><?php } ?>
                        <?php if (isset($service['work'])){ ?><div class="pb5"><?php echo $service['work'] ?> </div><?php } ?>
                        <?php if ($service->getCurrentPrice()){ ?>
                            <div class="font16 pb10">
                                <strong><?php echo number_format($service->getCurrentPrice(), 2, ',', ' '); ?> Р</strong>
                            </div>
                        <?php }                         
                    }
                    ?>
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