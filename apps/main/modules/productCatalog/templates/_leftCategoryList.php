<div class="catProductNum"><b>Всего <?php echo $quantity; ?> товаров</b></div>
<div class="line pb10"></div>
<dl class="bCtg">
    <dd>
        <ul>
        <?php
        $level = 1;  
        if ($currentCat->core_parent_id && isset($treeList)){
        foreach($treeList as $cat){
                echo '<li class="bCtg__eL'.$level.' mBold">
                        <a href="/catalog/'.$cat['token'].'/">
                            <span>'.$cat['name'].'</span>
                        </a>
                      </li>';
                $level++;
            }

            if (isset($brothersList)){
                foreach($brothersList as $brother){
                    if ($brother->countProduct() < 1) continue;
                    echo '<li class="bCtg__eL'.$level;
                    if ($brother['id'] == $currentCat->id) echo ' mSelected';
                    echo '">
                            <a href="/catalog/'.$brother['token'].'/">
                                <span>';
                    if ($brother['id'] != $currentCat->id) echo '<div>-</div>';                    
                    echo               $brother['name'].
                                '</span>
                            </a>
                          </li>';                    
                }
            } else {
                echo '<li  class="bCtg__eL'.$level.' mSelected">
                        <a href="/catalog/'.$currentCat->token.'/">
                            <span>'.$currentCat->name.'</span>
                        </a>
                      </li>';                  
            }
            $level++;
        }    
        ?>            
            
        <?php
        if (isset($list) && $list && count($list) > 0 )
        foreach($list as $cat){
            if ($cat->countProduct() < 1 || !$cat['is_active']) continue;
            echo '<li class="bCtg__eL'.$level.'">
                    <a href="/catalog/'.$cat['token'].'/">
                        <span>';
            if ($cat['level']>1) echo '<div>-</div>';
            echo            $cat['name'].
                        '</span>
                    </a>
                  </li>';
        }
        ?>
        </ul>
    </dd>
</dl>

