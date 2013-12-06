<style type="text/css">
    #debug-panel {
        position: fixed;
        bottom: 30px;
        left: 2px;
        z-index: 999;
        background: #000000;
        color: #43C6ED;
        opacity: 0.95;
        padding: 4px 6px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }

    #debug-panel a, #debug-panel a:hover {
        color: #ffffff;
        font-size: 12px;
        font-weight: normal;
        font-family: Courier New;
    }

    #debug-panel .content {
        display: block;
        max-height: 480px;
        max-width: 800px;
        overflow: auto;
    }

    #debug-panel .property {
        border-collapse: collapse;
    }

    #debug-panel .property td {
        vertical-align: top;
    }

    #debug-panel .property-name {
        padding: 5px 0 5px 22px;
        background-repeat: no-repeat;
        background-position: 0 3px;
    }

    #debug-panel .property-value {
        padding: 5px 10px 5px;
    }

    #debug-panel .property-value .query {
        padding: 5px 0 5px 0;
        background-repeat: no-repeat;
        background-position: 0 2px;
        text-decoration: none;
    }

    #debug-panel .property-value .query:hover {
        color: greenyellow;
    }

    #debug-panel .property-value .query-ok {
        color: limegreen;
    }
    #debug-panel .property-value .query-fail {
        color: red;
    }
    #debug-panel .property-value .query-default {
        color: #ffffff;
    }
    #debug-panel .property-value .query-cell {
        padding: 2px 10px 2px 0;
        white-space: nowrap;
    }
</style>

<script type="text/javascript">
    $('#debug-panel a').click(function(e) {
        e.preventDefault();
        console.info('debug cliclked');

        var parent = $(this).parent();
        var contentEl = parent.find('.content');

        var content = '<br /><table class="property">';
        $.each(parent.data('value'), function(i, item) {
            var type = item[1];
            var value = item[0];
            var icon = '/debug/icons/default.png';

            if (('id' == i) || ('env' == i) || ('route' == i) || ('act' == i) || ('sub.act' == i) || ('memory, Mb' == i) || ('time.main-menu' == i) || ('user' == i) || ('status' == i)) {
            } else if ('git' == i) {
                value = '<span style="color: #ffff00">' + value.version + '</span> ' + value.tag;
            } else if (('time.core' == i) || ('time.data-store' == i) || ('time.content' == i) || ('time.total' == i)) {
                value = value['time, ms'] + ' (' + value.count + ')';
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
                        + '<td class="query-cell"><a href="#" class="query ' + valueClass + '">' + item.url + (item.data ? ('<span style="color: #ededed"> --data ' + JSON.stringify(item.data) + '</span>') : '') + '</a></td>'
                        + '</tr>';

                })
                value += '</table>';
            } else {
                value = '<pre class="hidden">' + JSON.stringify(value, null, 4) + '</pre>';
            }

            if ('id' == i) {
                icon = '/debug/icons/id.png';
            } else if ('query' == i) {
                icon = '/debug/icons/query.png';
            } else if ('user' == i) {
                icon = '/debug/icons/user.png';
            } else if ('config' == i) {
                icon = '/debug/icons/config.png';
            } else if ('memory, Mb' == i) {
                icon = '/debug/icons/memory.png';
            } else if (0 === i.indexOf('time')) {
                icon = '/debug/icons/time.png';
            }

            content += (
                '<tr>'
                + '<td class="property-name" style="background-image: url(' + icon + ');' + (('info' != type) ? ('color: #ff0000;') : '') + '"><a href="#">' + i  + '</a></td>'
                + '<td class="property-value">' + value + '</td>'
                + '</tr>'
            );
        });
        content += '</table>';

        contentEl.html(content);
    });
</script>