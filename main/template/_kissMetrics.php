<? if (\App::config()->analytics['enabled']): ?>
<script type="text/javascript">
    if ( typeof(_kmq) !== 'undefined' ) {
    <? if (\App::abtestJson() && \App::abtestJson()->isActive()) : ?>
        _kmq.push(["set", {"abtest": "<%= \App::abTestJson()->getCase()->getGaEvent() %>"}]);
    <? endif ?>
    }
</script>
<? endif ?>