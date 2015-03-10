<? $f  = function (
    array $errors
) {
    foreach ($errors as $error) {
?>

    <div class="orderPayment_block orderPayment_block_error">
        <?= $error['code'] ?> <?= $error['message'] ?>
    </div>

<? }}; return $f;