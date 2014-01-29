<?php

return function(
    \Helper\TemplateHelper $helper,
    $url,
    $data,
    $result
) { ?>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />
<script src="http://code.jquery.com/jquery-1.8.3.js"></script>
    <style>
        .navbar-fixed-top{
            background: rgba(0,0,0,.2);
        }
        .navbar-inner{
            text-align: center;
            width: 80%;
            padding: .5em;
            left: 10%;
            position: relative;
        }
    </style>

<div class="container" style="padding-top: 20px;">
    <div class="row">
        <div class="col-md-12" style="padding-top: 40px;">
            <form action="<?= $helper->url('debug.query') ?>" method="post" role="form">
                <div class="form-group navbar-fixed-top">
                    <div class="navbar-inner">
                        <input autofocus="autofocus" type="text" class="form-control" placeholder="http://api.enter.ru" name="url" value="<?= $url ?>" width="100" />
                    </div>
                </div>

                <div class="form-group">
                    <a class="jsJson" data-target=".jsDebugData" href="#"><span class="glyphicon glyphicon-align-left"></span></a>
                    <textarea name="data" data-json-view="inline" class="form-control jsDebugData" rows="6" placeholder="{}"><?= (bool)$data ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-default">Выполнить</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <a class="jsJson" data-target=".jsDebugResult" href="#"><span class="glyphicon glyphicon-align-left"></span></a>
            <pre class="jsDebugResult" data-json-view="pretty" style="white-space: pre; overflow: scroll;"><?= htmlspecialchars(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8') ?></pre>
        </div>
    </div>

</div>


<script type="text/javascript">
    $('.jsJson').click(function(e) {
        e.preventDefault();

        var target =  $($(this).data('target'));
        if (!target) return false;

        if ('inline' == target.data('jsonView')) {
            target.text(
                JSON.stringify(
                    JSON.parse(target.text()),
                    null,
                    4
                )
            );
            target.data('jsonView', 'pretty');
        } else {
            target.text(
                JSON.stringify(
                    JSON.parse(target.text()),
                    null
                )
            );
            target.data('jsonView', 'inline');
        }

        $(this).blur();
    });
</script>

<? };