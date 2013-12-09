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

<script type="text/javascript">
    $('.debug-panel a').on('click', function(e) {
        e.preventDefault();
        console.info('debug cliclked');

        var parent = $(this).parent();
        var contentEl = parent.find('.content');

        if (parent.data('initialized')) {
            contentEl.toggle();
            return false;
        }

        var content = '<br /><table class="property">';
        $.each(parent.data('value'), function(i, item) {
            var type = item[1];
            var value = item[0];
            var icon = '/debug/icons/default.png';

            if (('id' == i) || ('env' == i) || ('route' == i) || ('act' == i) || ('sub.act' == i) || ('user' == i)) {
                value = '<span style="color: #ffffff">' + value + '</span>';
            } else if ('status' == i) {
                value = '<span style="color: ' + ((value > 300) ? '#ff0000' : '#00ff00') + '">' + value + '</span>' ;
            } else if ('git' == i) {
                value = '<span style="color: #ffff00">' + value.version + '</span> ' + value.tag;
            } else if ('timer' == i) {
                value = '<table>';
                $.each(item[0], function(i, item) {
                    value += '<tr><td class="query-cell">' + i + ': </td><td class="query-cell query-ok">' + item.value + ' ' + item.unit + ' (' + item.count + ')' + '</td></tr>';
                })
                value += '</table>';
            } else if ('memory' == i) {
                value = value.value + ' ' + value.unit;
            } else if (('error' == i) && (value[0])) {
                value = value[0];
                value = '<span style="color: #ff0000">#' + value.code + ' ' + value.message + '</span>';
            } else if ('query' == i) {
                value = '<table>';
                $.each(item[0], function(i, item) {
                    valueClass = 'query-default';
                    if (item.error) {
                        valueClass = 'query-fail';
                    } else if (item.url) {
                        valueClass = 'query-ok';
                    } else {
                        item.url = '';
                    }

                    value += '<tr>'
                        + '<td class="query-cell">'
                            + ((item.info && item.info.total_time) ? item.info.total_time : '')
                        + '</td>'
                        + '<td class="query-cell">'
                            + ((item.url && item.retryCount) ? item.retryCount : '')
                        + '</td>'
                        + '<td class="query-cell">'
                            + ((item.header && item.header['X-Server-Name']) ? item.header['X-Server-Name'] : '')
                            + ' '
                            + ((item.header && item.header['X-API-Mode']) ? item.header['X-API-Mode'] : '')
                        + '</td>'
                        + '<td class="query-cell"><a target="_blank" href="' + item.url + '" class="query ' + valueClass + '">' + item.escapedUrl + (item.data ? ('<span style="color: #ededed"> --data ' + JSON.stringify(item.data) + '</span>') : '') + '</a></td>'
                        + '</tr>';

                })
                value += '</table>';
            } else {
                value = '<pre class="hidden">' + JSON.stringify(value, null, 4) + '</pre>';
            }

            if (-1 !== $.inArray(i, ['id', 'env', 'git', 'query', 'user', 'config', 'memory', 'memory', 'timer', 'session', 'server', 'abTest', 'abTestJson'])) {
                icon = '/debug/icons/' + i + '.png';
            }

            content += (
                '<tr>'
                + '<td class="property-name" style="background-image: url(' + icon + ');"><a class="property-name-link" href="#" style="' + (('info' != type) ? ('color: #ff0000;') : '') + '">' + i  + '</a></td>'
                + '<td class="property-value">' + value + '</td>'
                + '</tr>'
            );
        });
        content += '</table>';

        contentEl.html(content);
        parent.data('initialized', true);
    });
</script>