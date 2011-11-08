<div class="catProductNum"><b>Всего <?php echo $quantity; ?> товаров</b></div>
<div class="line pb10"></div>
<dl class="bCtg">
    <dd>
        <ul>
        <?php
        $level = 1;  
        if ($currentCat->core_parent_id && isset($treeList)){
        foreach($treeList as $cat){
                echo '<li>
                        <a href="/catalog/'.$cat['token'].'/">
                            <span  class="bCtg__eL'.$level.' mBold">'.$cat['name'].'</span>
                        </a>
                      </li>';
                $level++;
            }

            if (isset($brothersList)){
                foreach($brothersList as $brother){
                    if ($brother->countProduct() < 1) continue;
                    echo '<li>
                            <a href="/catalog/'.$brother['token'].'/">
                                <span  class="bCtg__eL'.$level;
                    if ($brother['id'] == $currentCat->id) echo ' mSelected';
                    echo '">- '
                                   .$brother['name'].
                                '</span>
                            </a>
                          </li>';                    
                }
            } else {
                echo '<li>
                        <a href="/catalog/'.$currentCat->token.'/">
                            <span  class="bCtg__eL'.$level.' mSelected">'.$currentCat->name.'</span>
                        </a>
                      </li>';                  
            }
            $level++;
        }    
        ?>            
            
        <?php
        if (isset($list) && $list && count($list) > 0 )
        foreach($list as $cat){
            if ($cat->countProduct() < 1) continue;
            echo '<li>
                    <a href="/catalog/'.$cat['token'].'/">
                        <span  class="bCtg__eL'.$level.'">';
            if ($cat['level']>1) echo '- ';
            echo            $cat['name'].
                        '</span>
                    </a>
                  </li>';
        }
        ?>
        </ul>
    </dd>
</dl>

