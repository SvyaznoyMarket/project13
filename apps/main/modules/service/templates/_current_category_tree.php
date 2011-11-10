    <?php
    #print_r($serviceList);
    foreach($listInner as $item){

        if ($item['level'] == ($serviceCategory['level']+1)){ ?>
            <?php 
               $currentServiceList = array(); 
               foreach($serviceList as $service){
                    foreach($service['ServiceCategoryRelation'] as $cat){
                        if ($cat['category_id'] == $item['id']){
                            $currentServiceList[] = $service;
                        }
                    }
               }
               if (count($currentServiceList)<1) continue;
            ?>   
            <div class="serviceblock">
                <div class="photo"><a href=""><img width="160" height="120" alt="" src="<?php echo $item['image']; ?>"></a></div>
                <div class="info">
                    <h3><?php echo $item['name'] ?></h3>
                    <?php 
                    #print_r($currentServiceList);
                    foreach($currentServiceList as $service){ ?>
                        <div class="font16 pb8"><?php echo $service['name'] ?></div>  
                        <?php if (isset($service['description'])){ ?><div class="pb5"><?php echo $service['description'] ?> </div><?php } ?>
                        <?php if (isset($service['Price']) && isset($service['Price'][0]) && isset($service['Price'][0]['price'])){ ?>
                            <div class="font16 pb10">
                                <strong><?php echo intval($service['Price'][0]['price']); ?> Р</strong>
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