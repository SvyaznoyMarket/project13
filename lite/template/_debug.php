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
                    <tr>
                        <th>Time</th>
                        <th>Retry</th>
                        <th>Server</th>
                        <th>Mode</th>
                        <th title="Response body length (in bytes)">Length</th>
                        <th>Error</th>
                        <th>Query</th>
                    </tr>
                    {{#value}}
                    <tr>
                        <td class="query-cell">
                            {{#cache}}<span style="color: #ffff00">*</span>{{/cache}} {{info.total_time}} {{#spend}}({{spend}}){{/spend}}
                        </td>
                        <td class="query-cell" title="{{delay}}">
                            {{#delays}}
                            {{#selected}}
                            <span style="color: #ffa200;">{{value}}</span><sup>{{http_code}}</sup>
                            {{/selected}}
                            {{^selected}}
                            {{value}}<sup>{{http_code}}</sup>
                            {{/selected}}
                            {{/delays}}
                        </td>
                        <!--<td class="query-cell"><span title="Retry count">{{retryCount}}</span></td>-->
                        <td class="query-cell">{{header.X-Server-Name}}</td>
                        <td class="query-cell">{{header.X-API-Mode}}</td>
                        <td class="query-cell">{{responseBodyLength}}</td>
                        <td class="query-cell">{{#error}}({{code}}) <span title="{{message}}">{{&substrMessage}}</span>{{/error}}</td>
                        <td class="query-cell">
                            <a href="{{^data}}{{url}}{{/data}}{{#data}}/debug/query?data={{encodedData}}&url={{encodedUrl}}{{/data}}" target="_blank" class="openDirectly">&#11016;</a>
                            <form action="/debug/query" target="_blank" method="post" style="display: inline-block;">
                                <input type="hidden" value="{{data}}" name="data">
                                <input type="hidden" value="{{url}}" name="url">
                                <button type="submit" class="formButton" >
                                    <span class="query {{#error}}query-fail{{/error}} {{#url}}query-ok{{/url}}">{{url}}</span>
                                    {{#data}}{{data}}{{/data}}
                                </button>
                            </form>
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
    <div class="jsDebugPanelItem">
        <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#">{{name}}</a>
        <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
        <div class="debug-panel-item-content jsDebugPanelItemContent"></div>
    </div>
</script>

<script id="tpl-debug-googleAnalyticsCalls-row" type="text/html">
    <tr>
        <td>{{event.category}}</td>
        <td>{{event.action}}</td>
        <td>{{event.label}}</td>
        <td>{{event.value}}</td>
        <td>{{functionName}}</td>
        <td><div class="debug-panel-item-content-googleAnalyticsCalls-row-functionArguments">{{functionArguments}}</div></td>
    </tr>
</script>

<div class="jsDebugPanel debug-panel js-module-require" data-module="enter.debug" data-value="" data-prev-value="">
    <script type="application/json"><?= json_encode($debugData, JSON_UNESCAPED_UNICODE) ?></script>
    <script type="application/json"><?= json_encode($prevDebugData, JSON_UNESCAPED_UNICODE) ?></script>
    <a class="debug-panel-open jsOpenDebugPanelContent" href="#">debug</a>
    <div class="debug-panel-content jsDebugPanelContent">
        <div class="jsDebugPanelItem">
            <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#">Google analytics calls</a>
            <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
            <table class="debug-panel-item-content debug-panel-item-content-googleAnalyticsCalls jsDebugPanelItemContent js-debugPanel-googleAnalyticsCalls-content">
                <tr>
                    <th title="Event category">Category</th>
                    <th title="Event action">Action</th>
                    <th title="Event label">Label</th>
                    <th title="Event value">Value</th>
                    <th title="Google analytics function name">Function</th>
                    <th title="Google analytics function call arguments">Call arguments</th>
                </tr>
            </table>
        </div>
        <div class="debug-panel-item-prev jsDebugPanelItem">
            <a class="debug-panel-item-open jsOpenDebugPanelItem" href="#" title="Previous document debug"><?= $helper->escape($prevDebugData['server']['value']['REQUEST_URI']) ?></a>
            <a class="debug-panel-item-close jsCloseDebugPanelItem">×</a>
            <table class="debug-panel-item-content jsDebugPanelItemContent jsPrevDebugPanelItemContent"></table>
        </div>
        <div class="jsDebugPanelItem">
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

    .debug-panel .debug-panel-open-error, .debug-panel .debug-panel-open-error:hover {
        color: #fd6666;
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

    .debug-panel-item-content-googleAnalyticsCalls {
        border-collapse: collapse;
    }

    .debug-panel-item-content-googleAnalyticsCalls th, .debug-panel-item-content-googleAnalyticsCalls td {
        padding: 5px;
        text-align: left;
        border: 1px solid #ccc;
    }

    .debug-panel-item-content-googleAnalyticsCalls-row-functionArguments {
        max-width: 400px;
        overflow: auto;
        white-space: nowrap;
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

    .debug-panel th[title] {
        cursor: help;
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
        max-height: 160px;
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
    .debug-panel .property-value th[title] {
        cursor: help;
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

    .debug-panel .formButton {
        color: white;
        background: none;
        border: none;
    }
</style>