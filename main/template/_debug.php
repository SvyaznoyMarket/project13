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

<script id="tplDebugFirstLevelJira" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            {{name}}
        </td>
        <td class="property-value">
            <a target="_blank" href="{{value.url}}" style="color: #ffff00; text-decoration: underline;">{{value.version}}</a>
        </td>
    </tr>
</script>

<script id="tplDebugFirstLevelQuery" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            <a class="jsExpandValue" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded">
                <table>
                    <tbody>
                        {{#value}}
                            <tr>
                                <td class="query-cell">{{#cache}}<span style="color: #ffff00" title="Cached">*</span>{{/cache}} <span title="Response time">{{info.total_time}}</span></td>
                                <td class="query-cell"><span title="Retry count">{{retryCount}}</span></td>
                                <td class="query-cell"><span title="Server name">{{header.X-Server-Name}}</span></td>
                                <td class="query-cell">{{header.X-API-Mode}}</td>
                                <td class="query-cell"><span title="Delay">{{delay}}</span></td>
                                <td class="query-cell"><a href="{{^data}}{{url}}{{/data}}{{#data}}/debug/query?data={{encodedData}}&url={{encodedUrl}}{{/data}}" target="_blank" title="Open directly" class="openDirectly">&#11016;</a></td>
                                <td class="query-cell">
                                    <a class="query 
                                            {{#error}}
                                                query-fail
                                            {{/error}}
                                            {{#url}}
                                                query-ok
                                            {{/url}}"
                                        href="/debug/query?data={{encodedData}}&url={{encodedUrl}}" target="_blank">{{url}}</a>
                                    {{#data}}{{data}}{{/data}}
                                </td>
                            </tr>
                        {{/value}}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</script>


<script id="tplDebugFirstLevelConfig" type="text/html">
    <tr>
        <td style="background-image: url({{iconUrl}});" class="property-name">
            <a class="jsExpandValue" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded" style="display:none;">
                <table>
                    <tbody>
                    {{#value}}
                    <tr>
                        <td class="query-cell">{{name}}</td>
                        <td class="query-cell"><pre>{{value}}</pre></td>
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
            <a class="jsExpandValue" href="#">{{name}}</a>
        </td>
        <td class="property-value">
            <div class="jsExpandedValue property-value-expanded">
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
        <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#">{{name}}</a>
        <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
        <div class="debug-panel-item-content jsDebugPanelItemContent"></div>
    </div>
</script>

<div class="jsDebugPanel debug-panel" data-value="<?= $helper->json($debugData) ?>" data-prev-value="<?= $helper->json($prevDebugData) ?>">
    <a class="debug-panel-open jsOpenDebugPanelContent" href="#">debug</a>
    <div class="debug-panel-content jsDebugPanelContent">
        <div class="debug-panel-item-prev" title="Previous document debug">
            <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#"><?= $helper->escape($prevDebugData['server']['value']['REQUEST_URI']) ?></a>
            <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
            <table class="debug-panel-item-content jsDebugPanelItemContent jsPrevDebugPanelItemContent"></table>
        </div>
        <div>
            <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#"><?= $helper->escape($debugData['server']['value']['REQUEST_URI']) ?></a>
            <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
            <table class="debug-panel-item-content jsDebugPanelItemContent jsCurrentDebugPanelItemContent"></table>
        </div>
    </div>
</div>

<style type="text/css">
    .debug-panel-open, .debug-panel-item-open {
        padding: 5px;
        display: inline-block;
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
    .debug-panel-item-content {
        display: none;
        background: #0f1113;
        -webkit-border-radius: 4px;
        -webkit-border-top-left-radius: 0;
        -moz-border-radius: 4px;
        -moz-border-radius-topleft: 0;
        border-radius: 4px;
        border-top-left-radius: 0;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    .debug-panel-item-prev {
        opacity: 0.3;
    }

    .debug-panel-item-prev:hover {
        opacity: 1;
    }

    .debug-panel {
        position: fixed;
        bottom: 30px;
        left: 2px;
        z-index: 999999;
        color: #cdcdcd;
        font: normal 13px Courier New;
        opacity: 0.95;
        /*max-height: 280px;*/
        /*overflow: auto;*/
    }

    .debug-panel a, .debug-panel a:hover {
        color: #ffffff;
        font: normal 13px Courier New;
        text-decoration: none;
    }

    .debug-panel-content {
        display: none;
    }

    a.debug-panel-item-close, a.debug-panel-item-close:hover {
        display: inline-block;
        cursor: pointer;
        font-size: 16px;
        color: #000000;
        text-decoration: none;
    }

    .debug-panel .content {
        max-height: 200px;
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
        padding: 5px 5px 5px 25px;
        background-repeat: no-repeat;
        background-position: 5px 5px;
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
        max-height: 130px;
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
    .debug-panel .property-value .query-cell span[title] {
        cursor: help;
    }
    .debug-panel .openDirectly {
        color: #bebebe;
    }
</style>