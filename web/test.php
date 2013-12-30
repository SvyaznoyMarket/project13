<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title></title>
    <script src="http://code.jquery.com/jquery-1.8.3.js" type="text/javascript"></script>

    <script src="/js/partner/ARPlugin.js"></script>
    <script type="text/javascript">
        /*ARPlugin.init({
            type:"simple",
            js:"static/js/",
            css:"static/css/",
            img:"static/img/",
            swf:"static/swf/",
            resources:"static/resources/",
            meshes_path:"static/resources/model/",
            textures_path:"static/resources/model/",
            marker_path:"http://pandragames.ru/enter_marker.pdf"
        });*/


        var
            //d,
            loadFitting = function loadFitting() {
                console.log('### loadFitting');
                fittingPopupShow = function( e ) {
                    console.log('### fittingPopupShow');
                    console.log('### BEGIN');
                    e.preventDefault();
                    ARPlugin.show('watch_1.obj','watch_1.png');
                    console.log('### end');
                    return false;
                };

                ARPlugin.init({
                    type:"simple",
                    js:"/static/js/",
                    css:"/static/css/",
                    img:"/static/img/",
                    swf:"/static/swf/",
                    resources:"/static/resources/",
                    meshes_path:"/static/resources/model/",
                    textures_path:"/static/resources/model/",
                    marker_path:"http://pandragames.ru/enter_marker.pdf"
                });
                $('.vFitting').bind('click', fittingPopupShow);
            };

        $(document).ready(function() {
            if ( !false || true ) { // TODO
                hasFlash = true;
                loadFitting();
            }
        });
    </script>
</head>
<body>
<div style="width: 100%; text-align: center; margin-top: 100px;">
    <ul>
        <li class="vFitting">
            <a id="#runTest" style="text-decoration: none;color: black;font-size: 20px;font-family: arial, verdana, sans-serif; border: 2px solid; padding: 6px;" href="javascript:ARPlugin.show('watch_1.obj','watch_1.png')">
                Показать в 3D
            </a>
        </li>
    </ul>
</div>
</body>
</html>