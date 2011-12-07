<h2>Выбираем услуги</h2>
<div class="line pb10"></div>
<?php    
    $lastLevel = 0;
    foreach($list as $key => $item){
        if ( (!isset($list[$key+1]) || $list[$key+1]['level'] <= $item['level'])
                //&& !count($item['ServiceRelation'])
                ) {
            continue;
        }
        if ($item['level'] == 1){
            if ($lastLevel) echo '</ul>';
            echo '<h2>'.$item['name'].'</h2>';
        } elseif ($item['level'] == 2){
            if ($lastLevel == 1) echo '<ul class="leftmenu pb10">';
            echo '<li>';
            if ($serviceCategory['id'] == $item['id']) echo '<strong class="motton">'.$item['name'].'</strong>';
            else echo '<a href="'.url_for('service_list') . '/' . $item['token'].'">'.$item['name'].'</a>';
            echo '</li>';            
        }
        $lastLevel = $item['level'];
    }
?>
</ul>