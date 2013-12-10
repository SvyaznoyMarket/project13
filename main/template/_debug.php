<?php
/**
 * @var $helper    \Helper\TemplateHelper
 * @var $debugData array
 */
?>

<script id="firstLevel" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <span style="color: #ffffff">{{value}}</span>
        </td>
    </tr>
</script>

<script id="firstLevel-git" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <a target="_blank" href="https://github.com/SvyaznoyMarket/project13/tree/{{value.version}}" style="color: #ffff00; text-decoration: underline;">{{value.version}}</a> {{value.tag}}
        </td>
    </tr>
</script>

<script id="firstLevel-query" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
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
                                    href="/debug/query?data={{data}}&url={{url}}" target="_blank">{{escapedUrl}}</a>
                            </td>
                        </tr>
                    {{/value}}
                </tbody>
            </table>
        </td>
    </tr>
</script>

<script id="firstLevel-timer" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
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
        </td>
    </tr>
</script>

<script id="firstLevel-memory" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <span style="color: #ffff00;">{{value.value}}</span> {{value.unit}}
        </td>
    </tr>
</script>

<script id="firstLevel-hidden" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <a class="jsExpandValue" href="#">...</a>
            <div class="jsExpandedValue property-value-expanded"><pre>{{_data}}</pre></div>
        </td>
    </tr>
</script>

<div class="jsDebugPanel debug-panel" data-value="<?= $helper->json($debugData) ?>" style="position: fixed; bottom: 30px; left: 2px; z-index: 999;">
    <a class="jsOpenDebugPanel" href="#" style="padding-bottom: 10px;">debug</a>
    <table class="jsDebugPanelContent" style="display: none"></table>
</div>

<style type="text/css">
    .debug-panel {
        position: fixed;
        bottom: 30px;
        left: 2px;
        z-index: 999;
        background: #000000;
        color: #cdcdcd;
        opacity: 0.95;
        padding: 4px 6px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
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
        background: #222;
        color: limegreen;
        display: none;
        max-height: 300px;
        overflow: scroll;
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