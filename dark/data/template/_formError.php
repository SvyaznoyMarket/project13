<?php
/**
 * @var $errors string[]
 * @var $error string
 */
?>

<?php
if (isset($error)) {
    $errors = array($error);
}
?>

<ul class="error_list">
<? foreach ($errors as $error): ?>
    <li><?= $error ?></li>
<? endforeach ?>
</ul>
