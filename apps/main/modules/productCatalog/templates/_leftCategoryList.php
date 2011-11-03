<dl class="bCtg">
    <dt>В разделе <?php echo $quantity; ?> товаров.</dt>
    <dd>
        <ul>
        <?php
        $level = 1;  
        if ($currentCat->core_parent_id){
        foreach($treeList as $cat){
                echo '<li>
                        <a href="/catalog/'.$cat['token'].'/">
                            <span  class="bCtg__eL'.$level.'">'.$cat['name'].'</span>
                        </a>
                      </li>';
                $level++;
            }
            echo '<li>
                    <a href="/catalog/'.$currentCat->token.'/">
                        <span  class="bCtg__eL'.$level.' mSelected">'.$currentCat->name.'</span>
                    </a>
                  </li>';  
            $level++;
        }    
        ?>            
            
        <?php
        foreach($list as $cat){
            echo '<li>
                    <a href="/catalog/'.$cat['token'].'/">
                        <span  class="bCtg__eL'.$level.'">'.$cat['name'].'</span>
                    </a>
                  </li>';
        }
        ?>
        </ul>
    </dd>
</dl>

