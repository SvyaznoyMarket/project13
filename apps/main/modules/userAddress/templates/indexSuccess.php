<?php 
slot('title','Адреса доставки');
?>   
<div class="float100">
		<div class="column685 ">
            
            <div class="block">
              <?php if (count($userAddressList) > 0): ?>
                <?php include_component('userAddress', 'list', array('userAddressList' => $userAddressList)) ?>

              <?php else: ?>
                <p>нет адресов</p>

              <?php endif ?>

              <div class="block-inline">
                <?php include_component('userAddress', 'form', array('form' => $form)) ?>
              </div>
            </div>

        </div>
    </div>

    <?php include_component('user', 'menu') ?>
