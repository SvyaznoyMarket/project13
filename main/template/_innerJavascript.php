<? if (\App::config()->analytics): ?>
<script type="text/javascript">
    (function() {
        var s=document.createElement("script");s.src='http://crossss.com/crossssInfo.aspx?id=<?= \App::config()->crossss['id'] ?>';s.type="text/javascript";document.getElementsByTagName("HEAD")[0].appendChild(s);
    })();
</script>
<? endif ?>