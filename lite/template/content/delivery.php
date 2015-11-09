<?
/**
 * @var $points Model\Point\ScmsPoint[]
 * @var $content string
 * @var $page View\Content\DeliveryMapPage
 */
$helper = \App::helper();
?>

<?= $helper->jsonInScriptTag($points, 'points') ?>
<?= $content ?>

<script id="js-point-template" type="text/template" class="hidden">
    {{#point}}
    {{#shown}}
    <li class="points-lst-i js-pointpopup-pick-point" id="point_uid_{{uid}}" data-uid="{{uid}}">
        <div class="points-lst-i__partner jsPointListItemPartner">{{partner.name}}</div>

        <div class="deliv-item__addr">
            {{#subway}}
            <div class="deliv-item__metro" style="background: {{line.color}}">
                <div class="deliv-item__metro-inn">{{name}}</div>
            </div>
            {{/subway}}
            <div class="deliv-item__addr-name">{{address}}</div>
        </div>
    </li>
    {{/shown}}
    {{/point}}
</script>