<?php
/** @var $errors array */
?>

<?php if (!is_array($errors)) $errors = array($errors) ?>

<?php if ((bool)$errors): ?>
<ul class="error_list">
<?php foreach ($errors as $error) :?>
    <li><?php echo $error ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>