<?php
if ( !$currentCat || !$list ) {
    return;
}
if (!isset($currentDirectory)) $currentDirectory = array();
?>
<div class="catProductNum"><b>Всего <?php echo $quantity.($currentCat->has_line ? ' серий' : ' товаров') ?></b></div>
<div class="line pb10"></div>
<dl class="bCtg">



    <dd>
        <ul>
           <?php
           foreach($list as $key => $item) { ?>
                <?php
                if (!in_array($item['id'], $notFreeCatList) ) {
                    continue;
                }
                ?>
                <li class="bCtg__eL<?php echo $item['level']+1;
                if ($currentCat->level == 0 && $currentCat->id == $item['id']) echo " hidden";
                elseif (is_array($pathAr) && in_array($item['id'], $pathAr)) echo " mBold";
                elseif ($currentCat->id == $item['id']) echo " mSelected";
                elseif ($hasChildren && $item['core_parent_id'] == $currentCat->core_id) echo '';
                elseif (!$hasChildren && $item['core_parent_id'] == $currentCat->core_parent_id) echo '';
                else echo ' hidden';
                ?> ">
                    <a href="<?php echo url_for('productCatalog_category', array('productCategory' => $item['token_prefix'] ? ($item['token_prefix'].'/'.$item['token']) : $item['token'])); ?>">
                        <span><?php echo $item['name'] ?></span>
                    </a>
                </li>
           <?php
           }
           ?>


            <!--
            <?php $currentLevel1 = null; ?>
            <?php if ($currentDirectory->level > 0 ) { ?>
                <li class="bCtg__eL1 mBold">
                    <a href="<?php echo url_for('productCatalog_category', array('productCategory' => $root_info['token_prefix'] ? ($root_info['token_prefix'].'/'.$root_info['token']) : $root_info['token'])); ?>">
                        <span><?php echo $root_info['name'] ?></span>
                    </a>
                </li>
            <?php } ?>
            <?php foreach($root_list['children'] as $level1) {
                ?>
                <?php
                if ($level1->core_parent_id == $currentDirectory->core_id) {
                    $classCur = "";
                } else {
                    $classCur = " hidden";
                }

                if ($ancestorList[1]['id'] == $level1->id) {
                    $classCur = "mBold";
                } else if ($currentDirectory->id == $level1->id){
                    $classCur = "mSelected";
                    $currentLevel1 = $level1;
                }
                ?>
                <li class="bCtg__eL2 <?php echo $classCur ; ?>">
                    <a href="<?php echo url_for('productCatalog_category', array('productCategory' => $level1['token_prefix'] ? ($level1['token_prefix'].''.$level1['token']) : $level1['token'])); ?>">
                        <span>
                            <?php if ($currentDirectory->level > 0 ) { ?>
                                <div>-</div>
                            <?php } ?>
                            <?php echo $level1['name']; ?>
                        </span>
                    </a>
                </li>
                    <?php foreach($tree[$level1['id']]['children'] as $level2) { ?>
                    <?php

                    #echo count($tree[$level2->id]['children']) .'==<br>';
                    #echo $level2->id .'=='. $currentDirectory->id.'<br>';
                    if (isset($currentLevel1) && $level2->core_parent_id == $currentLevel1->core_id) {
                        $classCur1 = "";
                    } elseif ($level1->core_id == $currentDirectory->core_parent_id
                               && $currentDirectory->level == 2
                               && isset($tree[$currentDirectory->id])
                               && count($tree[$currentDirectory->id]['children']) < 1
                        ) {
                        $classCur1 = "";
                    } else {
                        $classCur1 = " hidden";
                    }

                    if ($ancestorList[2]['id'] == $level2->id) {
                        $classCur1 = "mBold";
                    } else if ($currentDirectory->id == $level2->id){
                        $classCur1 = "mSelected";
                        $currentLevel2 = $level2;
                    }
                    ?>
                    <li class="bCtg__eL3 <?php echo $classCur1; ?>">
                        <a href="<?php echo url_for('productCatalog_category', array('productCategory' => $level2['token_prefix'] ? ($level2['token_prefix'].'/'.$level2['token']) : $level2['token'])); ?>">
                            <span><div>-</div><?php echo $level2['name']; ?></span>
                        </a>
                    </li>
                    <?php $classParent = $classCur; ?>
                    <?php foreach($tree[$level2['id']]['children'] as $level3) { ?>
                        <?php

                        #echo $level2->core_id .'=='. $currentDirectory->core_parent_id.'<br>';

                        if (isset($currentLevel2) && $level3->core_parent_id == $currentLevel2->core_id) {
                            $classCur2 = "";
                        } elseif ($level2->core_id == $currentDirectory->core_parent_id
                                    && $currentDirectory->level == 3
                                    && isset($tree[$currentDirectory->id])
                                    && count($tree[$currentDirectory->id]['children']) < 1
                           ) {
                            $classCur2 = "";
                        } else {
                            $classCur2 = " hidden";
                        }

                        if ($ancestorList[3]['id'] == $level3['id']) {
                            $classCur2 = "mBold";
                        } else if ($currentDirectory['id'] == $level3['id']){
                            $classCur2 = "mSelected";
                        }
                        ?>
                        <li class="bCtg__eL4 <?php echo $classCur2; ?>">
                            <a href="<?php echo url_for('productCatalog_category', array('productCategory' => $level3['token_prefix'] ? ($level3['token_prefix'].'/'.$level3['token']) : $level3['token'])); ?>">
                                <span><div>-</div><?php echo $level3['name']; ?></span>
                            </a>
                        </li>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
            -->

        </ul>
    </dd>

</dl>

