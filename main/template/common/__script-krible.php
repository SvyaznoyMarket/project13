<!-- krible.ru Teleportator -->
<script type="text/javascript">
    var kribleCode = '5e14662e854af6384a9a84af28874dd8';
    var kribleTeleportParam = {'text': '#ffffff', 'button': '#ffa901', 'link':'#000000'};
    (function (d, w) {
        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function() {
                n.parentNode.insertBefore(s, n);
            };
        s.type = "text/javascript";
        s.async = true;
        s.src = 'http://chat.krible.ru/arena/'+
        kribleCode.substr(0,2)+'/'+kribleCode+'/teleport.js';
        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f);
        } else {
            f();
        }
    })(document, window);
</script>
<!-- /krible.ru Teleportator end -->