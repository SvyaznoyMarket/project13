<form method="post" action="<?php echo $paymentProvider->getConfig('url') ?>">
  <?php echo $paymentForm ?>

  <input type="submit" value="Отправить" />
</form>