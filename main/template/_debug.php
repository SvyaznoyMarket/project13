<?php
/**
 * @var $helper    \Helper\TemplateHelper
 * @var $debugData array
 */
?>

<script id="tplDebugFirstLevelDefault" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <span style="color: #ffffff">{{value}}</span>
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelGit" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <a target="_blank" href="https://github.com/SvyaznoyMarket/project13/tree/{{value.version}}" style="color: #ffff00; text-decoration: underline;">{{value.version}}</a> {{value.tag}}
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelQuery" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            <a class="jsExpandValue jsOpened" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded" style="display:block;">
                <table>
                    <tbody>
                        {{#value}}
                            <tr>
                                <td class="query-cell">{{info.total_time}}</td>
                                <td class="query-cell">{{retryCount}}</td>
                                <td class="query-cell">{{header.X-Server-Name}} {{header.X-API-Mode}}</td>
                                <td class="query-cell">
                                    <a class="query 
                                            {{#error}}
                                                query-fail
                                            {{/error}}
                                            {{#url}}
                                                query-ok
                                            {{/url}}"
                                        href="/debug/query?data={{data}}&url={{url}}" target="_blank">{{&escapedUrl}}</a>
                                    {{#data}}
                                    {{data}}
                                    {{/data}}
                                </td>
                            </tr>
                        {{/value}}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelTimer" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            <a class="jsExpandValue jsOpened" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded" style="display:block;">
                <table>
                    <tbody>
                        {{#value}}
                            <tr>
                                <td class="query-cell">{{name}}: </td>
                                <td class="query-cell query-ok">{{value}} {{unit}} ({{count}})</td>
                            </tr>
                        {{/value}}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelMemory" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            {{value.value}} {{value.unit}}
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelHidden" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            <a class="jsExpandValue" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded"><pre>{{_data}}</pre></div>
        </td>
    </tr>
</script>

<script id="tplDebugAjax" type="text/html">
    <div>
        <a class="jsOpenDebugPanel" href="#">{{name}}</a>
        <div class="jsDebugPanelContent" style="display: none"></div>
    </div>
</script>

<div class="jsDebugPanel debug-panel" data-value="<?= $helper->json($debugData) ?>" style="position: fixed; bottom: 30px; left: 2px; z-index: 999;">
    <div>
        <a class="jsOpenDebugPanel" href="#">debug</a>
        <table class="jsDebugPanelContent" style="display: none"></table>
    </div>
</div>

<style type="text/css">
    .jsOpenDebugPanel {
        padding: 5px;
        text-decoration: underline;
        display: block;
        margin: 4px 0 0;
        background: #0f1113;
        -webkit-border-top-left-radius: 4px;
        -webkit-border-top-right-radius: 4px;
        -moz-border-radius-topleft: 4px;
        -moz-border-radius-topright: 4px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
    .jsDebugPanelContent {
        background: #0f1113;
    }
    .jsOpenDebugPanel.jsOpened {
    }
    .debug-panel {
        position: fixed;
        bottom: 30px;
        left: 2px;
        z-index: 1100;
        color: #cdcdcd;
        opacity: 0.95;
    }

    .debug-panel a, .debug-panel a:hover {
        color: #ffffff;
        font: normal 13px Courier New;
    }

    .debug-panel .content {
        max-height: 600px;
        max-width: 800px;
        overflow: auto;
        padding: 0;
    }

    .debug-panel .property {
        border-collapse: collapse;
    }

    .debug-panel .property td {
        vertical-align: top;
        font: normal 13px Courier New;
    }

    .debug-panel .property-name {
        padding: 5px 0 5px 22px;
        background-repeat: no-repeat;
        background-position: 0 3px;
        vertical-align: top;
    }
    .debug-panel .property-name a {
        color: #68c5e1;
    }

    .debug-panel .property-value, .debug-panel .property-value td {
        font: normal 13px Courier New;
        color: #bebebe;
        padding: 5px 10px 5px;
    }

    .debug-panel .property-value .query {
        padding: 5px 0 5px 0;
        background-repeat: no-repeat;
        background-position: 0 2px;
        text-decoration: none;
    }

    .debug-panel .property-value .query:hover {
        color: greenyellow;
    }

    .debug-panel .property-value .query-ok {
        color: limegreen;
    }
    .debug-panel .property-value-expanded {
        color: limegreen;
        display: none;
        max-height: 400px;
        max-width: 1000px;
        overflow: auto;
    }
    .debug-panel .property-value .query-fail {
        color: red;
    }
    .debug-panel .property-value .query-fail:hover {
        color: palevioletred;
    }
    .debug-panel .property-value .query-default {
        color: #ffffff;
    }
    .debug-panel .property-value .query-cell {
        padding: 2px 10px 2px 0;
        white-space: nowrap;
    }
</style>