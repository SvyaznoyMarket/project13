/*
Copyright (c) 2009, deanlee (http://www.deanlee.cn)
*/
CKEDITOR.plugins.add('wpmore',{init:function(editor)
{editor.addCommand('wpmore',CKEDITOR.plugins.wpmoreCmd);editor.ui.addButton('wpMore',{label:'Insert More Tag',command:'wpmore',icon:this.path+'/images/more.gif'});editor.addCss('img.cke_wpmore'+'{'+'background-image: url('+CKEDITOR.getUrl(this.path+'images/more_bug.gif')+');'+'background-position: right center;'+'background-repeat: no-repeat;'+'clear: both;'+'display: block;'+'float: none;'+'width: 100%;'+'border-top: #999999 1px dotted;'+'height: 10px;'+'}');},afterInit:function(editor)
{var fakeCommentName='cke:wpmore';var dataProcessor=editor.dataProcessor,dataFilter=dataProcessor&&dataProcessor.dataFilter,htmlFilter=dataProcessor.htmlFilter,commentFilters=htmlFilter._.comment,filter=commentFilters.filter||commentFilters[0];if(dataFilter)
{dataFilter.addRules({comment:function(contents)
{var data=filter(contents);if(data.value=='<!--more-->')
{var fakeWrapper=new CKEDITOR.htmlParser.element(fakeCommentName,{});fakeWrapper.add(data);var fakeElement=editor.createFakeParserElement(fakeWrapper,'cke_wpmore',fakeCommentName,false);return fakeElement;}
return contents;}});}
var rule={elements:{}};rule.elements[fakeCommentName]=function(element)
{delete element.name;}
htmlFilter.addRules(rule);},requires:['fakeobjects']});CKEDITOR.plugins.wpmoreCmd={exec:function(editor)
{var fakeWrapper=CKEDITOR.dom.element.createFromHtml('<cke:wpmore><!--more--></cke:wpmore>',editor.document)
var fakeElement=editor.createFakeElement(fakeWrapper,'cke_wpmore','cke:wpmore');var ranges=editor.getSelection().getRanges();for(var range,i=0;i<ranges.length;i++)
{range=ranges[i];if(i>0)
breakObject=breakObject.clone(true);range.insertNode(fakeElement);}}};