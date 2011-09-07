<div class="form">
  <div class="form-row">
    <label>Сумма</label>
    <div class="content"><?php echo number_format($form->getSum(), 0, ',', ' ').' руб' ?></div>
  </div>
</div>

<form action="<?php echo $form->getUrl()?>" method="post">
  <ul>
    <?php echo $form ?>
  </ul>

  <input type="submit" value="Оплатить" />
</form>