<?php

return function(
    \Helper\TemplateHelper $helper,
    $url,
    $data,
    $result
) { ?>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />

<div class="container" style="padding-top: 20px;">
    <div class="row">
        <form action="<?= $helper->url('debug.query') ?>" method="post" role="form">
            <div class="form-group">
                <input type="text" class="form-control" placeholder="http://api.enter.ru" name="url" value="<?= $url ?>" width="100" />
            </div>

            <div class="form-group">
                <textarea name="data" class="form-control" placeholder="{}"><?= (bool)$data ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '' ?></textarea>
            </div>

            <button type="submit" class="btn btn-default">Выполнить</button>
        </form>
    </div>

    <div class="row">
        <hr />
        <pre style="white-space: pre; overflow: scroll;"><?= htmlspecialchars(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8') ?></pre>
    </div>

</div>

<? };