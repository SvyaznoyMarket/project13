<?php

return function(
    \Helper\TemplateHelper $helper,
    $url,
    $data,
    $result,
    $isShow = false
) { ?>

<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" />
<script src="//yandex.st/jquery/2.1.1/jquery.min.js"></script>
<style>
        .navbar-fixed-top{
            background: rgba(0,0,0,.2);
        }
        .navbar-inner{
            text-align: center;
            width: 80%;
            padding: .5em;
            left: 10%;
            position: relative;
        }
        .jsonview {
            font-family: monospace;
            font-size: 1.1em;
            white-space: pre-wrap; }
        .jsonview .prop {
            font-weight: bold; }
        .jsonview .null {
            color: red; }
        .jsonview .bool {
            color: blue; }
        .jsonview .num {
            color: blue; }
        .jsonview .string {
            color: green;
            white-space: pre-wrap; }
        .jsonview .string.multiline {
            display: inline-block;
            vertical-align: text-top; }
        .jsonview .collapser {
            position: absolute;
            left: -1em;
            cursor: pointer; }
        .jsonview .collapsible {
            transition: height 1.2s;
            transition: width 1.2s; }
        .jsonview .collapsible.collapsed {
            height: .8em;
            width: 1em;
            display: inline-block;
            overflow: hidden;
            margin: 0; }
        .jsonview .collapsible.collapsed:before {
            content: "…";
            width: 1em;
            margin-left: .2em; }
        .jsonview .collapser.collapsed {
            transform: rotate(0deg); }
        .jsonview .q {
            display: inline-block;
            width: 0px;
            color: transparent; }
        .jsonview li {
            position: relative; }
        .jsonview ul {
            list-style: none;
            margin: 0 0 0 2em;
            padding: 0; }
        .jsonview h1 {
            font-size: 1.2em; }
    </style>

<div class="container" style="padding-top: 20px;">
    <div class="row">
        <div class="col-md-12" style="padding-top: 40px;">
            <form action="<?= $helper->url('debug.query') ?>" method="post" role="form">
                <input type="hidden" value="<?= $isShow ? '1' : '0' ?>" name="isShow">
                <div class="form-group navbar-fixed-top">
                    <div class="navbar-inner">
                        <input autofocus="autofocus" type="text" class="form-control" placeholder="http://api.enter.ru" name="url" value="<?= $url ?>" width="100" />
                    </div>
                </div>

                <div class="form-group">
                    <a class="jsJson" data-target=".jsDebugData" href="#"><span class="glyphicon glyphicon-align-left"></span></a>
                    <textarea name="data" data-json-view="inline" class="form-control jsDebugData" rows="6" placeholder="{}"><?= (bool)$data ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES) : '' ?></textarea>
                </div>

                <button type="submit" class="btn btn-default">Выполнить</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div style="display: none" id="jsonDiv" data-value="<?= $helper->json($result)?>"></div>
            <a class="jsJson" data-target=".jsDebugResult" href="#"><span class="glyphicon glyphicon-align-left"></span></a>
            <a class="jsBeautify" href="#">Beautify</a>
            <a class="jsCollapse beautify" href="#">Collapse</a>
            <a class="jsExpand beautify" href="#">Expand</a>
            <a class="jsToggle1 beautify" href="#">Toggle level 1</a>
            <a class="jsToggle2 beautify" href="#">Toggle level 2</a>
            <pre class="jsDebugResult" data-json-view="pretty" style="white-space: pre; overflow: scroll;"><?= htmlspecialchars(json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS), ENT_QUOTES, 'UTF-8') ?></pre>
        </div>
    </div>

</div>


<script type="text/javascript">
(function($) {

    var JS = $('#jsonDiv').data('value'),
        beautifyButton = $('.beautify');

    beautifyButton.hide();

    $('.jsJson').click(function(e) {
        e.preventDefault();

        var target =  $($(this).data('target'));
        if (!target) return false;

        if ('inline' == target.data('jsonView')) {
            target.text(
                JSON.stringify(
                    JSON.parse(target.text()),
                    null,
                    4
                )
            );
            target.data('jsonView', 'pretty');
        } else {
            target.text(
                JSON.stringify(
                    JSON.parse(target.text()),
                    null
                )
            );
            target.data('jsonView', 'inline');
        }

        $(this).blur();
    });

    $('.jsBeautify').on('click',function(){
        $('.jsDebugResult').JSONView(JS, {collapsed: true});
        beautifyButton.show();
        $(this).hide();
    });
    $('.jsCollapse').on('click', function(){
        $('.jsDebugResult').JSONView('collapse');
    });
    $('.jsExpand').on('click', function(){
        $('.jsDebugResult').JSONView('expand');
    });
    $('.jsToggle1').on('click', function(){
        $('.jsDebugResult').JSONView('toggle', 1);
    });
    $('.jsToggle2').on('click', function(){
        $('.jsDebugResult').JSONView('toggle', 2);
    })
}(window.jQuery))
</script>

<script>
    // https://github.com/yesmeck/jquery-jsonview/
    (function(jQuery) {
        var $, Collapser, JSONFormatter, JSONView;
        JSONFormatter = (function() {
            function JSONFormatter(options) {
                if (options == null) {
                    options = {};
                }
                this.options = options;
            }

            JSONFormatter.prototype.htmlEncode = function(html) {
                if (html !== null) {
                    return html.toString().replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
                } else {
                    return '';
                }
            };

            JSONFormatter.prototype.jsString = function(s) {
                s = JSON.stringify(s).slice(1, -1);
                return this.htmlEncode(s);
            };

            JSONFormatter.prototype.decorateWithSpan = function(value, className) {
                return "<span class=\"" + className + "\">" + (this.htmlEncode(value)) + "</span>";
            };

            JSONFormatter.prototype.valueToHTML = function(value, level) {
                var valueType;
                if (level == null) {
                    level = 0;
                }
                valueType = Object.prototype.toString.call(value).match(/\s(.+)]/)[1].toLowerCase();
                return this["" + valueType + "ToHTML"].call(this, value, level);
            };

            JSONFormatter.prototype.nullToHTML = function(value) {
                return this.decorateWithSpan('null', 'null');
            };

            JSONFormatter.prototype.numberToHTML = function(value) {
                return this.decorateWithSpan(value, 'num');
            };

            JSONFormatter.prototype.stringToHTML = function(value) {
                var multilineClass, newLinePattern;
                if (/^(http|https|file):\/\/[^\s]+$/i.test(value)) {
                    return "<a href=\"" + (this.htmlEncode(value)) + "\"><span class=\"q\">\"</span>" + (this.jsString(value)) + "<span class=\"q\">\"</span></a>";
                } else {
                    multilineClass = '';
                    value = this.jsString(value);
                    if (this.options.nl2br) {
                        newLinePattern = /([^>\\r\\n]?)(\\r\\n|\\n\\r|\\r|\\n)/g;
                        if (newLinePattern.test(value)) {
                            multilineClass = ' multiline';
                            value = (value + '').replace(newLinePattern, '$1' + '<br />');
                        }
                    }
                    return "<span class=\"string" + multilineClass + "\">\"" + value + "\"</span>";
                }
            };

            JSONFormatter.prototype.booleanToHTML = function(value) {
                return this.decorateWithSpan(value, 'bool');
            };

            JSONFormatter.prototype.arrayToHTML = function(array, level) {
                var collapsible, hasContents, index, numProps, output, value, _i, _len;
                if (level == null) {
                    level = 0;
                }
                hasContents = false;
                output = '';
                numProps = array.length;
                for (index = _i = 0, _len = array.length; _i < _len; index = ++_i) {
                    value = array[index];
                    hasContents = true;
                    output += '<li>' + this.valueToHTML(value, level + 1);
                    if (numProps > 1) {
                        output += ',';
                    }
                    output += '</li>';
                    numProps--;
                }
                if (hasContents) {
                    collapsible = level === 0 ? '' : ' collapsible';
                    return "[<ul class=\"array level" + level + collapsible + "\">" + output + "</ul>]";
                } else {
                    return '[ ]';
                }
            };

            JSONFormatter.prototype.objectToHTML = function(object, level) {
                var collapsible, hasContents, numProps, output, prop, value;
                if (level == null) {
                    level = 0;
                }
                hasContents = false;
                output = '';
                numProps = 0;
                for (prop in object) {
                    numProps++;
                }
                for (prop in object) {
                    value = object[prop];
                    hasContents = true;
                    output += "<li><span class=\"prop\"><span class=\"q\">\"</span>" + (this.jsString(prop)) + "<span class=\"q\">\"</span></span>: " + (this.valueToHTML(value, level + 1));
                    if (numProps > 1) {
                        output += ',';
                    }
                    output += '</li>';
                    numProps--;
                }
                if (hasContents) {
                    collapsible = level === 0 ? '' : ' collapsible';
                    return "{<ul class=\"obj level" + level + collapsible + "\">" + output + "</ul>}";
                } else {
                    return '{ }';
                }
            };

            JSONFormatter.prototype.jsonToHTML = function(json) {
                return "<div class=\"jsonview\">" + (this.valueToHTML(json)) + "</div>";
            };

            return JSONFormatter;

        })();
        (typeof module !== "undefined" && module !== null) && (module.exports = JSONFormatter);
        Collapser = {
            bindEvent: function(item, collapsed) {
                var collapser;
                collapser = document.createElement('div');
                collapser.className = 'collapser';
                collapser.innerHTML = collapsed ? '+' : '-';
                collapser.addEventListener('click', (function(_this) {
                    return function(event) {
                        return _this.toggle(event.target);
                    };
                })(this));
                item.insertBefore(collapser, item.firstChild);
                if (collapsed) {
                    return this.collapse(collapser);
                }
            },
            expand: function(collapser) {
                var ellipsis, target;
                target = this.collapseTarget(collapser);
                ellipsis = target.parentNode.getElementsByClassName('ellipsis')[0];
                target.parentNode.removeChild(ellipsis);
                target.style.display = '';
                return collapser.innerHTML = '-';
            },
            collapse: function(collapser) {
                var ellipsis, target;
                target = this.collapseTarget(collapser);
                target.style.display = 'none';
                ellipsis = document.createElement('span');
                ellipsis.className = 'ellipsis';
                ellipsis.innerHTML = ' &hellip; ';
                target.parentNode.insertBefore(ellipsis, target);
                return collapser.innerHTML = '+';
            },
            toggle: function(collapser) {
                var target;
                target = this.collapseTarget(collapser);
                if (target.style.display === 'none') {
                    return this.expand(collapser);
                } else {
                    return this.collapse(collapser);
                }
            },
            collapseTarget: function(collapser) {
                var target, targets;
                targets = collapser.parentNode.getElementsByClassName('collapsible');
                if (!targets.length) {
                    return;
                }
                return target = targets[0];
            }
        };
        $ = jQuery;
        JSONView = {
            collapse: function(el) {
                if (el.innerHTML === '-') {
                    return Collapser.collapse(el);
                }
            },
            expand: function(el) {
                if (el.innerHTML === '+') {
                    return Collapser.expand(el);
                }
            },
            toggle: function(el) {
                return Collapser.toggle(el);
            }
        };
        return $.fn.JSONView = function() {
            var args, defaultOptions, formatter, json, method, options, outputDoc;
            args = arguments;
            if (JSONView[args[0]] != null) {
                method = args[0];
                return this.each(function() {
                    var $this, level;
                    $this = $(this);
                    if (args[1] != null) {
                        level = args[1];
                        return $this.find(".jsonview .collapsible.level" + level).siblings('.collapser').each(function() {
                            return JSONView[method](this);
                        });
                    } else {
                        return $this.find('.jsonview > ul > li > .collapsible').siblings('.collapser').each(function() {
                            return JSONView[method](this);
                        });
                    }
                });
            } else {
                json = args[0];
                options = args[1] || {};
                defaultOptions = {
                    collapsed: false,
                    nl2br: false
                };
                options = $.extend(defaultOptions, options);
                formatter = new JSONFormatter({
                    nl2br: options.nl2br
                });
                if (Object.prototype.toString.call(json) === '[object String]') {
                    json = JSON.parse(json);
                }
                outputDoc = formatter.jsonToHTML(json);
                return this.each(function() {
                    var $this, item, items, _i, _len, _results;
                    $this = $(this);
                    $this.html(outputDoc);
                    items = $this[0].getElementsByClassName('collapsible');
                    _results = [];
                    for (_i = 0, _len = items.length; _i < _len; _i++) {
                        item = items[_i];
                        if (item.parentNode.nodeName === 'LI') {
                            _results.push(Collapser.bindEvent(item.parentNode, options.collapsed));
                        } else {
                            _results.push(void 0);
                        }
                    }
                    return _results;
                });
            }
        };
    })(jQuery);
    </script>

<? };