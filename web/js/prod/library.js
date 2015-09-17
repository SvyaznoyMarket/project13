
if(typeof JSON!=='object'){JSON={};}
(function(){'use strict';function f(n){return n<10?'0'+n:n;}
if(typeof Date.prototype.toJSON!=='function'){Date.prototype.toJSON=function(){return isFinite(this.valueOf())?this.getUTCFullYear()+'-'+
f(this.getUTCMonth()+1)+'-'+
f(this.getUTCDate())+'T'+
f(this.getUTCHours())+':'+
f(this.getUTCMinutes())+':'+
f(this.getUTCSeconds())+'Z':null;};String.prototype.toJSON=Number.prototype.toJSON=Boolean.prototype.toJSON=function(){return this.valueOf();};}
var cx=/[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,escapable=/[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,gap,indent,meta={'\b':'\\b','\t':'\\t','\n':'\\n','\f':'\\f','\r':'\\r','"':'\\"','\\':'\\\\'},rep;function quote(string){escapable.lastIndex=0;return escapable.test(string)?'"'+string.replace(escapable,function(a){var c=meta[a];return typeof c==='string'?c:'\\u'+('0000'+a.charCodeAt(0).toString(16)).slice(-4);})+'"':'"'+string+'"';}
function str(key,holder){var i,k,v,length,mind=gap,partial,value=holder[key];if(value&&typeof value==='object'&&typeof value.toJSON==='function'){value=value.toJSON(key);}
if(typeof rep==='function'){value=rep.call(holder,key,value);}
switch(typeof value){case'string':return quote(value);case'number':return isFinite(value)?String(value):'null';case'boolean':case'null':return String(value);case'object':if(!value){return'null';}
gap+=indent;partial=[];if(Object.prototype.toString.apply(value)==='[object Array]'){length=value.length;for(i=0;i<length;i+=1){partial[i]=str(i,value)||'null';}
v=partial.length===0?'[]':gap?'[\n'+gap+partial.join(',\n'+gap)+'\n'+mind+']':'['+partial.join(',')+']';gap=mind;return v;}
if(rep&&typeof rep==='object'){length=rep.length;for(i=0;i<length;i+=1){if(typeof rep[i]==='string'){k=rep[i];v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}else{for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=str(k,value);if(v){partial.push(quote(k)+(gap?': ':':')+v);}}}}
v=partial.length===0?'{}':gap?'{\n'+gap+partial.join(',\n'+gap)+'\n'+mind+'}':'{'+partial.join(',')+'}';gap=mind;return v;}}
if(typeof JSON.stringify!=='function'){JSON.stringify=function(value,replacer,space){var i;gap='';indent='';if(typeof space==='number'){for(i=0;i<space;i+=1){indent+=' ';}}else if(typeof space==='string'){indent=space;}
rep=replacer;if(replacer&&typeof replacer!=='function'&&(typeof replacer!=='object'||typeof replacer.length!=='number')){throw new Error('JSON.stringify');}
return str('',{'':value});};}
if(typeof JSON.parse!=='function'){JSON.parse=function(text,reviver){var j;function walk(holder,key){var k,v,value=holder[key];if(value&&typeof value==='object'){for(k in value){if(Object.prototype.hasOwnProperty.call(value,k)){v=walk(value,k);if(v!==undefined){value[k]=v;}else{delete value[k];}}}}
return reviver.call(holder,key,value);}
text=String(text);cx.lastIndex=0;if(cx.test(text)){text=text.replace(cx,function(a){return'\\u'+
('0000'+a.charCodeAt(0).toString(16)).slice(-4);});}
if(/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,'@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,']').replace(/(?:^|:|,)(?:\s*\[)+/g,''))){j=eval('('+text+')');return typeof reviver==='function'?walk({'':j},''):j;}
throw new SyntaxError('JSON.parse');};}}());
function isTrueEmail(){var t=this.toString(),re=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;return re.test(t);}
String.prototype.isEmail=isTrueEmail;
(function(global){global.printPrice=function(price){price=String(price);price=price.replace(',','.');price=price.replace(/\s/g,'');price=String(Number(price).toFixed(2));price=price.split('.');if(price[0].length>=5){price[0]=price[0].replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g,'$1&thinsp;');}
if(price[1]==0){price=price.slice(0,1);}
return price.join('.');};}(this));
;(function(global){global.docCookies={getItem:function(sKey){return unescape(document.cookie.replace(new RegExp('(?:(?:^|.*;)\\s*'+escape(sKey).replace(/[\-\.\+\*]/g,'\\$&')+'\\s*\\=\\s*([^;]*).*$)|^.*$'),'$1'))||null;},setItem:function(sKey,sValue,vEnd,sPath,sDomain,bSecure){if(!sKey||/^(?:expires|max\-age|path|domain|secure)$/i.test(sKey)){return false;}
var sExpires='';if(vEnd){switch(vEnd.constructor){case Number:sExpires=vEnd===Infinity?'; expires=Fri, 31 Dec 9999 23:59:59 GMT':'; max-age='+vEnd;break;case String:sExpires='; expires='+vEnd;break;case Date:sExpires='; expires='+vEnd.toGMTString();break;}}
document.cookie=escape(sKey)+'='+escape(sValue)+sExpires+(sDomain?'; domain='+sDomain:'')+(sPath?'; path='+sPath:'')+(bSecure?'; secure':'');return true;},removeItem:function(sKey,sPath,sDomain){if(!sKey||!this.hasItem(sKey)){return false;}
document.cookie=escape(sKey)+'=; expires=Thu, 01 Jan 1970 00:00:00 GMT'+(sDomain?'; domain='+sDomain:'')+(sPath?'; path='+sPath:'');return true;},hasItem:function(sKey){return(new RegExp('(?:^|;\\s*)'+escape(sKey).replace(/[\-\.\+\*]/g,'\\$&')+'\\s*\\=')).test(document.cookie);},keys:function(){var aKeys=document.cookie.replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g,'').split(/\s*(?:\=[^;]*)?;\s*/);for(var nIdx=0;nIdx<aKeys.length;nIdx++){aKeys[nIdx]=unescape(aKeys[nIdx]);}
return aKeys;}};}(this));
(function(){var cache={};this.tmpl=function tmpl(str,data){var fn=!/\W/.test(str)?cache[str]=cache[str]||tmpl(document.getElementById(str).innerHTML):new Function("obj","var p=[],print=function(){p.push.apply(p,arguments);};"+"with(obj){p.push('"+
str.replace(/[\r\t\n]/g," ").split("<%").join("\t").replace(/((^|%>)[^\t]*)'/g,"$1\r").replace(/\t=(.*?)%>/g,"',$1,'").split("\t").join("');").split("%>").join("p.push('").split("\r").join("\\'")+"');}return p.join('');");return data?fn(data):fn;};})();
function FormValidator(config){if(!config.fields.length){return;}
this.config=$.extend({},this._defaultsConfig,config);this._enableHandlers();}
FormValidator.prototype._defaultsConfig={errorClass:'mError'};FormValidator.prototype._validateOnChangeFields={};FormValidator.prototype._requireAs={checkbox:function(fieldNode){var
value=fieldNode.attr('checked');if(value===undefined){return{hasError:true,errorMsg:'Поле обязательно для заполнения'};}
return{hasError:false};},radio:function(fieldNode){var
checked=fieldNode.filter(':checked').val();if(checked===undefined){return{hasError:true,errorMsg:'Необходимо выбрать пункт из списка'};}
return{hasError:false};},text:function(fieldNode){var
value=fieldNode.val();if(value.length===0){return{hasError:true,errorMsg:'Поле обязательно для заполнения'};}
return{hasError:false};},password:function(fieldNode){var
value=fieldNode.val();if(value.length===0){return{hasError:true,errorMsg:'Поле обязательно для заполнения'};}
return{hasError:false};},textarea:function(fieldNode){var
value=fieldNode.val();if(value.length===0){return{hasError:true,errorMsg:'Поле обязательно для заполнения'};}
return{hasError:false};},select:function(fieldNode){if(fieldNode.val()){return{hasError:false};}
return{hasError:true,errorMsg:'Необходимо выбрать значение из списка'};}};FormValidator.prototype._validBy={isEmail:function(fieldNode){var
re=/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i,value=fieldNode.val();if(re.test(value)){return{hasError:false};}
else{return{hasError:true,errorMsg:'Некорректно введен e-mail'};}},isPhone:function(fieldNode){var
re=/(\+7|8)(-|\s)?(\(\d(-|\s)?\d(-|\s)?\d\s?\)|\d(-|\s)?\d(-|\s)?\d\s?)(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d(-|\s)?\d$/i,value=fieldNode.val();if(re.test(value)){return{hasError:false};}
else{return{hasError:true,errorMsg:'Некорректно введен телефон'};}},isNumber:function(fieldNode){var
re=/^[0-9]+$/,value=fieldNode.val();if(re.test(value)){return{hasError:false};}
else{return{hasError:true,errorMsg:'Поле может содержать только числа'};}}};FormValidator.prototype._validateField=function(field){var
self=this,elementType=null,fieldNode=null,validBy=null,require=null,customErr='',error={hasError:false},result={};fieldNode=field.fieldNode;require=(fieldNode.attr('required')==='required')?true:field.require;validBy=field.validBy;customErr=field.customErr;if(!fieldNode.length){console.warn('нет поля, не валидируем');return error;}
elementType=(fieldNode.prop('tagName')==='TEXTAREA')?'textarea':(fieldNode.prop('tagName')==='SELECT')?'select':fieldNode.attr('type');if(require){if(self._requireAs.hasOwnProperty(elementType)){result=self._requireAs[elementType](fieldNode);if(result.hasError){error={hasError:true,errorMsg:(customErr!==undefined)?customErr:result.errorMsg};return error;}}
else{error={hasError:true,errorMsg:'Обязательное поле. Неизвестный метод проверки для '+elementType};return error;}}
if(self._validBy.hasOwnProperty(validBy)&&field.fieldNode.val().length!==0){result=self._validBy[validBy](fieldNode);if(result.hasError){error={hasError:true,errorMsg:(customErr!==undefined)?customErr:result.errorMsg};}}
else if(validBy!==undefined&&field.fieldNode.val().length!==0){error={hasError:true,errorMsg:'Неизвестный метод валидации '+validBy};}
return error;};FormValidator.prototype._unmarkFieldError=function(fieldNode){console.info('Снимаем маркировку');fieldNode.removeClass(this.config.errorClass);fieldNode.parent().find('.bErrorText').remove();};FormValidator.prototype._markFieldError=function(fieldNode,errorMsg){var
self=this;var
clearError=function clearError(){self._unmarkFieldError($(this));};console.info('маркируем');console.log(errorMsg);fieldNode.addClass(this.config.errorClass);fieldNode.before('<div class="bErrorText"><div class="bErrorText__eInner">'+errorMsg+'</div></div>');fieldNode.bind('focus',clearError);};FormValidator.prototype._enableHandlers=function(){var
self=this,fields=this.config.fields,currentField=null,i;var
validateOnBlur=function validateOnBlur(that){var
result={},findedField=self._findFieldByNode(that);if(findedField.finded){result=self._validateField(findedField.field);if(result.hasError){self._markFieldError(that,result.errorMsg);}}
else{that.unbind('blur',validateOnBlur);}
return false;},blurHandler=function blurHandler(){var
that=$(this),timeout_id=null;clearTimeout(timeout_id);timeout_id=window.setTimeout(function(){validateOnBlur(that);},5);};for(i=fields.length-1;i>=0;i--){currentField=fields[i];if(currentField.fieldNode.length===0){continue;}
if(currentField.validateOnChange){if(self._validateOnChangeFields[currentField.fieldNode.get(0).outerHTML]){continue;}
currentField.fieldNode.bind('blur',blurHandler);self._validateOnChangeFields[currentField.fieldNode.get(0).outerHTML]=true;}}};FormValidator.prototype._findFieldByNode=function(nodeToFind){var
fields=this.config.fields,i;for(i=fields.length-1;i>=0;i--){if(fields[i].fieldNode.get(0)===nodeToFind.get(0)){return{finded:true,field:fields[i],index:i};}}
return{finded:false};};FormValidator.prototype.validate=function(callbacks){var
self=this,fields=this.config.fields,i=0,errors=[],result={};for(i=fields.length-1;i>=0;i--){result=self._validateField(fields[i]);console.log(result);if(result.hasError){self._markFieldError(fields[i].fieldNode,result.errorMsg);errors.push({fieldNode:fields[i].fieldNode,errorMsg:result.errorMsg});}
else{console.log('нет ошибки в поле ');console.log(fields[i].fieldNode);self._unmarkFieldError(fields[i].fieldNode);}}
if(errors.length){callbacks.onInvalid(errors);}
else{callbacks.onValid();}};FormValidator.prototype.getValidate=function(fieldToFind){var
findedField=this._findFieldByNode(fieldToFind);if(findedField.finded){return findedField.field;}
return false;};FormValidator.prototype.setValidate=function(fieldNodeToCange,paramsToChange){var
findedField=this._findFieldByNode(fieldNodeToCange),addindField=null;if(findedField.finded){addindField=$.extend({},findedField.field,paramsToChange);this.config.fields.splice(findedField.index,1);}
else{paramsToChange.fieldNode=fieldNodeToCange;addindField=paramsToChange;}
this.addFieldToValidate(addindField);};FormValidator.prototype.removeFieldToValidate=function(fieldNodeToRemove){var
findedField=this._findFieldByNode(fieldNodeToRemove);if(findedField.finded){this.config.fields.splice(findedField.index,1);return true;}
return false;};FormValidator.prototype.addFieldToValidate=function(field){this.config.fields.push(field);this._enableHandlers();};
String.prototype.addParameterToUrl=function(key,value){var url=this.toString();var re=new RegExp('([?|&])'+key+'=.*?(&|#|$)(.*)','gi');if(re.test(url)){if(typeof value!=='undefined'&&value!==null){return url.replace(re,'$1'+key+'='+value+'$2$3');}
else{return url.replace(re,'$1$3').replace(/(&|\?)$/,'');}}
else{if(typeof value!=='undefined'&&value!==null){var separator=url.indexOf('?')!==-1?'&':'?',hash=url.split('#');url=hash[0]+separator+key+'='+value;if(hash[1]){url+='#'+hash[1];}
return url;}
else{return url;}}};
!function(root,module){var
CountDown=module();if(typeof define==='function'&&define.amd){define('CountDown',[],function(){return CountDown;});}else if(typeof exports!=='undefined'){exports.CountDown=CountDown;}else if(typeof modules!=='undefined'&&typeof modules.define==='function'){modules.define('CountDown',[],function(provide){provide(CountDown);});}else{root.CountDown=CountDown;}}(this,function(){'use strict';var
CountDown=(function(){var
ONE_SEC=1000,ONE_MIN=60*ONE_SEC,ONE_HOUR=60*ONE_MIN,ONE_DAY=24*ONE_HOUR,SUCCESSFUL_COMPLETE=true,helpers={isObject:function(obj){var
type=typeof obj;return type==='function'||type==='object'&&!!obj;},isFunction:function(obj){return typeof obj=='function'||false;},isNumber:function(obj){return toString.call(obj)==='[object Number]'&&!isNaN(obj);}},updateCounter=function(){var
now=(new Date()).getTime(),end=this.endDate.getTime(),diff=end-now;if(diff<=0){console.log('Достигли даты завершения');this.stop(SUCCESSFUL_COMPLETE);}
this.tick&&this.tick({days:Math.floor(diff/ONE_DAY),hours:Math.floor((diff%ONE_DAY)/ONE_HOUR),minutes:Math.floor(((diff%ONE_DAY)%ONE_HOUR)/ONE_MIN),seconds:Math.floor((((diff%ONE_DAY)%ONE_HOUR)%ONE_MIN)/ONE_SEC)});};CountDown=function CountDown(options){if(!(this instanceof CountDown)){return new CountDown(options);}
if(!helpers.isObject(options)){throw new Error('Параметры должны быть объектом');}
if(!helpers.isNumber(options.timestamp)){throw new Error('Параметр "timestamp" должен быть объектом');}
if(!helpers.isFunction(options.tick)){throw new Error('Параметр "tick" должен быть функцией');}
if(options.success&&!helpers.isFunction(options.success)){throw new Error('Параметр "success" должен быть функцией');}
this.endDate=new Date(options.timestamp);this.tick=options.tick;this.success=options.success;this.iid=setInterval(updateCounter.bind(this),1000);};CountDown.prototype.stop=function(successfulComplete){clearInterval(this.iid);if(successfulComplete&&this.success){this.success();}};return CountDown;}());return CountDown;});
;(function(){(function(){var clone=docCookies.setItem;docCookies.setItem=function(){var args=Array.prototype.slice.call(arguments);if(typeof args[4]=='undefined')args[4]='.'+/[A-Za-z0-9]+\.[A-Za-z0-9]+$/.exec(window.location.hostname)[0];return clone.apply(this,args);};})();(function(){var clone=docCookies.removeItem;docCookies.removeItem=function(){var args=Array.prototype.slice.call(arguments);if(typeof args[2]=='undefined')args[2]='.'+/[A-Za-z0-9]+\.[A-Za-z0-9]+$/.exec(window.location.hostname)[0];return clone.apply(this,args);};})();}());
;(function(definition){if(typeof define=="function"){define(definition);}else if(typeof YUI=="function"){YUI.add("es5",definition);}else{definition();}})(function(){function Empty(){}
if(!Function.prototype.bind){Function.prototype.bind=function bind(that){var target=this;if(typeof target!="function"){throw new TypeError("Function.prototype.bind called on incompatible "+target);}
var args=_Array_slice_.call(arguments,1);var bound=function(){if(this instanceof bound){var result=target.apply(this,args.concat(_Array_slice_.call(arguments)));if(Object(result)===result){return result;}
return this;}else{return target.apply(that,args.concat(_Array_slice_.call(arguments)));}};if(target.prototype){Empty.prototype=target.prototype;bound.prototype=new Empty();Empty.prototype=null;}
return bound;};}
var call=Function.prototype.call;var prototypeOfArray=Array.prototype;var prototypeOfObject=Object.prototype;var _Array_slice_=prototypeOfArray.slice;var _toString=call.bind(prototypeOfObject.toString);var owns=call.bind(prototypeOfObject.hasOwnProperty);var defineGetter;var defineSetter;var lookupGetter;var lookupSetter;var supportsAccessors;if((supportsAccessors=owns(prototypeOfObject,"__defineGetter__"))){defineGetter=call.bind(prototypeOfObject.__defineGetter__);defineSetter=call.bind(prototypeOfObject.__defineSetter__);lookupGetter=call.bind(prototypeOfObject.__lookupGetter__);lookupSetter=call.bind(prototypeOfObject.__lookupSetter__);}
if([1,2].splice(0).length!=2){var array_splice=Array.prototype.splice;var array_push=Array.prototype.push;var array_unshift=Array.prototype.unshift;if(function(){function makeArray(l){var a=[];while(l--){a.unshift(l)}
return a}
var array=[],lengthBefore;array.splice.bind(array,0,0).apply(null,makeArray(20));array.splice.bind(array,0,0).apply(null,makeArray(26));lengthBefore=array.length;array.splice(5,0,"XXX");if(lengthBefore+1==array.length){return true;}}()){Array.prototype.splice=function(start,deleteCount){if(!arguments.length){return[];}else{return array_splice.apply(this,[start===void 0?0:start,deleteCount===void 0?(this.length-start):deleteCount].concat(_Array_slice_.call(arguments,2)))}};}
else{Array.prototype.splice=function(start,deleteCount){var result,args=_Array_slice_.call(arguments,2),addElementsCount=args.length;if(!arguments.length){return[];}
if(start===void 0){start=0;}
if(deleteCount===void 0){deleteCount=this.length-start;}
if(addElementsCount>0){if(deleteCount<=0){if(start==this.length){array_push.apply(this,args);return[];}
if(start==0){array_unshift.apply(this,args);return[];}}
result=_Array_slice_.call(this,start,start+deleteCount);args.push.apply(args,_Array_slice_.call(this,start+deleteCount,this.length));args.unshift.apply(args,_Array_slice_.call(this,0,start));args.unshift(0,this.length);array_splice.apply(this,args);return result;}
return array_splice.call(this,start,deleteCount);}}}
if([].unshift(0)!=1){var array_unshift=Array.prototype.unshift;Array.prototype.unshift=function(){array_unshift.apply(this,arguments);return this.length;};}
if(!Array.isArray){Array.isArray=function isArray(obj){return _toString(obj)=="[object Array]";};}
var boxedString=Object("a"),splitString=boxedString[0]!="a"||!(0 in boxedString);var boxedForEach=true;if(Array.prototype.forEach){Array.prototype.forEach.call("foo",function(item,i,obj){if(typeof obj!=='object')boxedForEach=false;});}
if(!Array.prototype.forEach||!boxedForEach){Array.prototype.forEach=function forEach(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,thisp=arguments[1],i=-1,length=self.length>>>0;if(_toString(fun)!="[object Function]"){throw new TypeError();}
while(++i<length){if(i in self){fun.call(thisp,self[i],i,object);}}};}
if(!Array.prototype.map){Array.prototype.map=function map(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0,result=Array(length),thisp=arguments[1];if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
for(var i=0;i<length;i++){if(i in self)
result[i]=fun.call(thisp,self[i],i,object);}
return result;};}
if(!Array.prototype.filter){Array.prototype.filter=function filter(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0,result=[],value,thisp=arguments[1];if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
for(var i=0;i<length;i++){if(i in self){value=self[i];if(fun.call(thisp,value,i,object)){result.push(value);}}}
return result;};}
if(!Array.prototype.every){Array.prototype.every=function every(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0,thisp=arguments[1];if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
for(var i=0;i<length;i++){if(i in self&&!fun.call(thisp,self[i],i,object)){return false;}}
return true;};}
if(!Array.prototype.some){Array.prototype.some=function some(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0,thisp=arguments[1];if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
for(var i=0;i<length;i++){if(i in self&&fun.call(thisp,self[i],i,object)){return true;}}
return false;};}
if(!Array.prototype.reduce){Array.prototype.reduce=function reduce(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0;if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
if(!length&&arguments.length==1){throw new TypeError("reduce of empty array with no initial value");}
var i=0;var result;if(arguments.length>=2){result=arguments[1];}else{do{if(i in self){result=self[i++];break;}
if(++i>=length){throw new TypeError("reduce of empty array with no initial value");}}while(true);}
for(;i<length;i++){if(i in self){result=fun.call(void 0,result,self[i],i,object);}}
return result;};}
if(!Array.prototype.reduceRight){Array.prototype.reduceRight=function reduceRight(fun){var object=toObject(this),self=splitString&&_toString(this)=="[object String]"?this.split(""):object,length=self.length>>>0;if(_toString(fun)!="[object Function]"){throw new TypeError(fun+" is not a function");}
if(!length&&arguments.length==1){throw new TypeError("reduceRight of empty array with no initial value");}
var result,i=length-1;if(arguments.length>=2){result=arguments[1];}else{do{if(i in self){result=self[i--];break;}
if(--i<0){throw new TypeError("reduceRight of empty array with no initial value");}}while(true);}
if(i<0){return result;}
do{if(i in this){result=fun.call(void 0,result,self[i],i,object);}}while(i--);return result;};}
if(!Array.prototype.indexOf||([0,1].indexOf(1,2)!=-1)){Array.prototype.indexOf=function indexOf(sought){var self=splitString&&_toString(this)=="[object String]"?this.split(""):toObject(this),length=self.length>>>0;if(!length){return-1;}
var i=0;if(arguments.length>1){i=toInteger(arguments[1]);}
i=i>=0?i:Math.max(0,length+i);for(;i<length;i++){if(i in self&&self[i]===sought){return i;}}
return-1;};}
if(!Array.prototype.lastIndexOf||([0,1].lastIndexOf(0,-3)!=-1)){Array.prototype.lastIndexOf=function lastIndexOf(sought){var self=splitString&&_toString(this)=="[object String]"?this.split(""):toObject(this),length=self.length>>>0;if(!length){return-1;}
var i=length-1;if(arguments.length>1){i=Math.min(i,toInteger(arguments[1]));}
i=i>=0?i:length-Math.abs(i);for(;i>=0;i--){if(i in self&&sought===self[i]){return i;}}
return-1;};}
if(!Object.keys){var hasDontEnumBug=true,dontEnums=["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"],dontEnumsLength=dontEnums.length;for(var key in{"toString":null}){hasDontEnumBug=false;}
Object.keys=function keys(object){if((typeof object!="object"&&typeof object!="function")||object===null){throw new TypeError("Object.keys called on a non-object");}
var keys=[];for(var name in object){if(owns(object,name)){keys.push(name);}}
if(hasDontEnumBug){for(var i=0,ii=dontEnumsLength;i<ii;i++){var dontEnum=dontEnums[i];if(owns(object,dontEnum)){keys.push(dontEnum);}}}
return keys;};}
var negativeDate=-62198755200000,negativeYearString="-000001";if(!Date.prototype.toISOString||(new Date(negativeDate).toISOString().indexOf(negativeYearString)===-1)){Date.prototype.toISOString=function toISOString(){var result,length,value,year,month;if(!isFinite(this)){throw new RangeError("Date.prototype.toISOString called on non-finite value.");}
year=this.getUTCFullYear();month=this.getUTCMonth();year+=Math.floor(month/12);month=(month%12+12)%12;result=[month+1,this.getUTCDate(),this.getUTCHours(),this.getUTCMinutes(),this.getUTCSeconds()];year=((year<0?"-":(year>9999?"+":""))+
("00000"+Math.abs(year)).slice(0<=year&&year<=9999?-4:-6));length=result.length;while(length--){value=result[length];if(value<10){result[length]="0"+value;}}
return(year+"-"+result.slice(0,2).join("-")+"T"+result.slice(2).join(":")+"."+
("000"+this.getUTCMilliseconds()).slice(-3)+"Z");};}
var dateToJSONIsSupported=false;try{dateToJSONIsSupported=(Date.prototype.toJSON&&new Date(NaN).toJSON()===null&&new Date(negativeDate).toJSON().indexOf(negativeYearString)!==-1&&Date.prototype.toJSON.call({toISOString:function(){return true;}}));}catch(e){}
if(!dateToJSONIsSupported){Date.prototype.toJSON=function toJSON(key){var o=Object(this),tv=toPrimitive(o),toISO;if(typeof tv==="number"&&!isFinite(tv)){return null;}
toISO=o.toISOString;if(typeof toISO!="function"){throw new TypeError("toISOString property is not callable");}
return toISO.call(o);};}
if(!Date.parse||"Date.parse is buggy"){Date=(function(NativeDate){function Date(Y,M,D,h,m,s,ms){var length=arguments.length;if(this instanceof NativeDate){var date=length==1&&String(Y)===Y?new NativeDate(Date.parse(Y)):length>=7?new NativeDate(Y,M,D,h,m,s,ms):length>=6?new NativeDate(Y,M,D,h,m,s):length>=5?new NativeDate(Y,M,D,h,m):length>=4?new NativeDate(Y,M,D,h):length>=3?new NativeDate(Y,M,D):length>=2?new NativeDate(Y,M):length>=1?new NativeDate(Y):new NativeDate();date.constructor=Date;return date;}
return NativeDate.apply(this,arguments);};var isoDateExpression=new RegExp("^"+"(\\d{4}|[\+\-]\\d{6})"+"(?:-(\\d{2})"+"(?:-(\\d{2})"+"(?:"+"T(\\d{2})"+":(\\d{2})"+"(?:"+":(\\d{2})"+"(?:(\\.\\d{1,}))?"+")?"+"("+"Z|"+"(?:"+"([-+])"+"(\\d{2})"+":(\\d{2})"+")"+")?)?)?)?"+"$");var months=[0,31,59,90,120,151,181,212,243,273,304,334,365];function dayFromMonth(year,month){var t=month>1?1:0;return(months[month]+
Math.floor((year-1969+t)/4)-
Math.floor((year-1901+t)/100)+
Math.floor((year-1601+t)/400)+
365*(year-1970));}
function toUTC(t){return Number(new NativeDate(1970,0,1,0,0,0,t));}
for(var key in NativeDate){Date[key]=NativeDate[key];}
Date.now=NativeDate.now;Date.UTC=NativeDate.UTC;Date.prototype=NativeDate.prototype;Date.prototype.constructor=Date;Date.parse=function parse(string){var match=isoDateExpression.exec(string);if(match){var year=Number(match[1]),month=Number(match[2]||1)-1,day=Number(match[3]||1)-1,hour=Number(match[4]||0),minute=Number(match[5]||0),second=Number(match[6]||0),millisecond=Math.floor(Number(match[7]||0)*1000),isLocalTime=Boolean(match[4]&&!match[8]),signOffset=match[9]==="-"?1:-1,hourOffset=Number(match[10]||0),minuteOffset=Number(match[11]||0),result;if(hour<(minute>0||second>0||millisecond>0?24:25)&&minute<60&&second<60&&millisecond<1000&&month>-1&&month<12&&hourOffset<24&&minuteOffset<60&&day>-1&&day<(dayFromMonth(year,month+1)-
dayFromMonth(year,month))){result=((dayFromMonth(year,month)+day)*24+
hour+
hourOffset*signOffset)*60;result=((result+minute+minuteOffset*signOffset)*60+
second)*1000+millisecond;if(isLocalTime){result=toUTC(result);}
if(-8.64e15<=result&&result<=8.64e15){return result;}}
return NaN;}
return NativeDate.parse.apply(this,arguments);};return Date;})(Date);}
if(!Date.now){Date.now=function now(){return new Date().getTime();};}
if(!Number.prototype.toFixed||(0.00008).toFixed(3)!=='0.000'||(0.9).toFixed(0)==='0'||(1.255).toFixed(2)!=='1.25'||(1000000000000000128).toFixed(0)!=="1000000000000000128"){(function(){var base,size,data,i;base=1e7;size=6;data=[0,0,0,0,0,0];function multiply(n,c){var i=-1;while(++i<size){c+=n*data[i];data[i]=c%base;c=Math.floor(c/base);}}
function divide(n){var i=size,c=0;while(--i>=0){c+=data[i];data[i]=Math.floor(c/n);c=(c%n)*base;}}
function toString(){var i=size;var s='';while(--i>=0){if(s!==''||i===0||data[i]!==0){var t=String(data[i]);if(s===''){s=t;}else{s+='0000000'.slice(0,7-t.length)+t;}}}
return s;}
function pow(x,n,acc){return(n===0?acc:(n%2===1?pow(x,n-1,acc*x):pow(x*x,n/2,acc)));}
function log(x){var n=0;while(x>=4096){n+=12;x/=4096;}
while(x>=2){n+=1;x/=2;}
return n;}
Number.prototype.toFixed=function(fractionDigits){var f,x,s,m,e,z,j,k;f=Number(fractionDigits);f=f!==f?0:Math.floor(f);if(f<0||f>20){throw new RangeError("Number.toFixed called with invalid number of decimals");}
x=Number(this);if(x!==x){return"NaN";}
if(x<=-1e21||x>=1e21){return String(x);}
s="";if(x<0){s="-";x=-x;}
m="0";if(x>1e-21){e=log(x*pow(2,69,1))-69;z=(e<0?x*pow(2,-e,1):x/pow(2,e,1));z*=0x10000000000000;e=52-e;if(e>0){multiply(0,z);j=f;while(j>=7){multiply(1e7,0);j-=7;}
multiply(pow(10,j,1),0);j=e-1;while(j>=23){divide(1<<23);j-=23;}
divide(1<<j);multiply(1,1);divide(2);m=toString();}else{multiply(0,z);multiply(1<<(-e),0);m=toString()+'0.00000000000000000000'.slice(2,2+f);}}
if(f>0){k=m.length;if(k<=f){m=s+'0.0000000000000000000'.slice(0,f-k+2)+m;}else{m=s+m.slice(0,k-f)+'.'+m.slice(k-f);}}else{m=s+m;}
return m;}}());}
var string_split=String.prototype.split;if('ab'.split(/(?:ab)*/).length!==2||'.'.split(/(.?)(.?)/).length!==4||'tesst'.split(/(s)*/)[1]==="t"||''.split(/.?/).length||'.'.split(/()()/).length>1){(function(){var compliantExecNpcg=/()??/.exec("")[1]===void 0;String.prototype.split=function(separator,limit){var string=this;if(separator===void 0&&limit===0)
return[];if(Object.prototype.toString.call(separator)!=="[object RegExp]"){return string_split.apply(this,arguments);}
var output=[],flags=(separator.ignoreCase?"i":"")+
(separator.multiline?"m":"")+
(separator.extended?"x":"")+
(separator.sticky?"y":""),lastLastIndex=0,separator=new RegExp(separator.source,flags+"g"),separator2,match,lastIndex,lastLength;string+="";if(!compliantExecNpcg){separator2=new RegExp("^"+separator.source+"$(?!\\s)",flags);}
limit=limit===void 0?-1>>>0:limit>>>0;while(match=separator.exec(string)){lastIndex=match.index+match[0].length;if(lastIndex>lastLastIndex){output.push(string.slice(lastLastIndex,match.index));if(!compliantExecNpcg&&match.length>1){match[0].replace(separator2,function(){for(var i=1;i<arguments.length-2;i++){if(arguments[i]===void 0){match[i]=void 0;}}});}
if(match.length>1&&match.index<string.length){Array.prototype.push.apply(output,match.slice(1));}
lastLength=match[0].length;lastLastIndex=lastIndex;if(output.length>=limit){break;}}
if(separator.lastIndex===match.index){separator.lastIndex++;}}
if(lastLastIndex===string.length){if(lastLength||!separator.test("")){output.push("");}}else{output.push(string.slice(lastLastIndex));}
return output.length>limit?output.slice(0,limit):output;};}());}else if("0".split(void 0,0).length){String.prototype.split=function(separator,limit){if(separator===void 0&&limit===0)return[];return string_split.apply(this,arguments);}}
if("".substr&&"0b".substr(-1)!=="b"){var string_substr=String.prototype.substr;String.prototype.substr=function(start,length){return string_substr.call(this,start<0?((start=this.length+start)<0?0:start):start,length);}}
var ws="\x09\x0A\x0B\x0C\x0D\x20\xA0\u1680\u180E\u2000\u2001\u2002\u2003"+"\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028"+"\u2029\uFEFF";if(!String.prototype.trim||ws.trim()){ws="["+ws+"]";var trimBeginRegexp=new RegExp("^"+ws+ws+"*"),trimEndRegexp=new RegExp(ws+ws+"*$");String.prototype.trim=function trim(){if(this===void 0||this===null){throw new TypeError("can't convert "+this+" to object");}
return String(this).replace(trimBeginRegexp,"").replace(trimEndRegexp,"");};}
function toInteger(n){n=+n;if(n!==n){n=0;}else if(n!==0&&n!==(1/0)&&n!==-(1/0)){n=(n>0||-1)*Math.floor(Math.abs(n));}
return n;}
function isPrimitive(input){var type=typeof input;return(input===null||type==="undefined"||type==="boolean"||type==="number"||type==="string");}
function toPrimitive(input){var val,valueOf,toString;if(isPrimitive(input)){return input;}
valueOf=input.valueOf;if(typeof valueOf==="function"){val=valueOf.call(input);if(isPrimitive(val)){return val;}}
toString=input.toString;if(typeof toString==="function"){val=toString.call(input);if(isPrimitive(val)){return val;}}
throw new TypeError();}
var toObject=function(o){if(o==null){throw new TypeError("can't convert "+o+" to object");}
return Object(o);};});
(function(root,factory){if(typeof define==='function'&&define.amd){define([],factory);}else if(typeof module!=="undefined"&&module.exports){module.exports=factory();}else{root.lscache=factory();}}(this,function(){var CACHE_PREFIX='lscache-';var CACHE_SUFFIX='-cacheexpiration';var EXPIRY_RADIX=10;var EXPIRY_UNITS=60*1000;var MAX_DATE=Math.floor(8.64e15/EXPIRY_UNITS);var cachedStorage;var cachedJSON;var cacheBucket='';var warnings=false;function supportsStorage(){var key='__lscachetest__';var value=key;if(cachedStorage!==undefined){return cachedStorage;}
try{setItem(key,value);removeItem(key);cachedStorage=true;}catch(e){if(isOutOfSpace(e)){cachedStorage=true;}else{cachedStorage=false;}}
return cachedStorage;}
function isOutOfSpace(e){if(e&&e.name==='QUOTA_EXCEEDED_ERR'||e.name==='NS_ERROR_DOM_QUOTA_REACHED'||e.name==='QuotaExceededError'){return true;}
return false;}
function supportsJSON(){if(cachedJSON===undefined){cachedJSON=(window.JSON!=null);}
return cachedJSON;}
function expirationKey(key){return key+CACHE_SUFFIX;}
function currentTime(){return Math.floor((new Date().getTime())/EXPIRY_UNITS);}
function getItem(key){return localStorage.getItem(CACHE_PREFIX+cacheBucket+key);}
function setItem(key,value){localStorage.removeItem(CACHE_PREFIX+cacheBucket+key);localStorage.setItem(CACHE_PREFIX+cacheBucket+key,value);}
function removeItem(key){localStorage.removeItem(CACHE_PREFIX+cacheBucket+key);}
function eachKey(fn){var prefixRegExp=new RegExp('^'+CACHE_PREFIX+cacheBucket+'(.*)');for(var i=localStorage.length-1;i>=0;--i){var key=localStorage.key(i);key=key&&key.match(prefixRegExp);key=key&&key[1];if(key&&key.indexOf(CACHE_SUFFIX)<0){fn(key,expirationKey(key));}}}
function flushItem(key){var exprKey=expirationKey(key);removeItem(key);removeItem(exprKey);}
function flushExpiredItem(key){var exprKey=expirationKey(key);var expr=getItem(exprKey);if(expr){var expirationTime=parseInt(expr,EXPIRY_RADIX);if(currentTime()>=expirationTime){removeItem(key);removeItem(exprKey);return true;}}}
function warn(message,err){if(!warnings)return;if(!('console'in window)||typeof window.console.warn!=='function')return;window.console.warn("lscache - "+message);if(err)window.console.warn("lscache - The error was: "+err.message);}
var lscache={set:function(key,value,time){if(!supportsStorage())return;if(typeof value!=='string'){if(!supportsJSON())return;try{value=JSON.stringify(value);}catch(e){return;}}
try{setItem(key,value);}catch(e){if(isOutOfSpace(e)){var storedKeys=[];var storedKey;eachKey(function(key,exprKey){var expiration=getItem(exprKey);if(expiration){expiration=parseInt(expiration,EXPIRY_RADIX);}else{expiration=MAX_DATE;}
storedKeys.push({key:key,size:(getItem(key)||'').length,expiration:expiration});});storedKeys.sort(function(a,b){return(b.expiration-a.expiration);});var targetSize=(value||'').length;while(storedKeys.length&&targetSize>0){storedKey=storedKeys.pop();warn("Cache is full, removing item with key '"+key+"'");flushItem(storedKey.key);targetSize-=storedKey.size;}
try{setItem(key,value);}catch(e){warn("Could not add item with key '"+key+"', perhaps it's too big?",e);return;}}else{warn("Could not add item with key '"+key+"'",e);return;}}
if(time){setItem(expirationKey(key),(currentTime()+time).toString(EXPIRY_RADIX));}else{removeItem(expirationKey(key));}},get:function(key){if(!supportsStorage())return null;if(flushExpiredItem(key)){return null;}
var value=getItem(key);if(!value||!supportsJSON()){return value;}
try{return JSON.parse(value);}catch(e){return value;}},remove:function(key){if(!supportsStorage())return;flushItem(key);},supported:function(){return supportsStorage();},flush:function(){if(!supportsStorage())return;eachKey(function(key){flushItem(key);});},flushExpired:function(){if(!supportsStorage())return;eachKey(function(key){flushExpiredItem(key);});},setBucket:function(bucket){cacheBucket=bucket;},resetBucket:function(){cacheBucket='';},enableWarnings:function(enabled){warnings=enabled;}};return lscache;}));
;(function(global){var pageConfig=global.ENTER.config.pageConfig,utils=global.ENTER.utils;utils.packageReq=function packageReq(reqArray){console.info('Выполнение пакетного запроса',reqArray);var
dataToSend={},callbacks=[],i,len;dataToSend.actions=[];var
resHandler=function resHandler(res){var
i,len;console.info('Обработка ответа пакетого запроса',res);if(res.success===false||(res.actions&&res.actions.length===0)){console.warn('Route false');console.log(res.success);console.log(res.actions);}
for(i=0,len=res.actions.length-1;i<=len;i++){callbacks[i](res.actions[i]);}};for(i=0,len=reqArray.length-1;i<=len;i++){if(!(reqArray[i]&&reqArray[i].url)){console.info('continue');continue;}
dataToSend.actions.push({url:reqArray[i].url,method:reqArray[i].type,data:reqArray[i].data||null});callbacks[i]=reqArray[i].callback;}
if(!dataToSend.actions.length){return;}
$.ajax({url:pageConfig.routeUrl,type:'POST',data:dataToSend,success:resHandler});};}(this));
;(function(ENTER){var $body=$(document.body),utils=ENTER.utils;utils.trim=function(string){return((string||'')+'').replace(/^\s+|\s+$/g,'');};utils.objLen=function objLen(obj){var
len=0,p;for(p in obj){if(obj.hasOwnProperty(p)){len++;}}
return len;};utils.getURLParam=function getURLParam(paramName,url){var result=new RegExp('[\\?&]'+utils.escapeRegexp(encodeURIComponent(paramName))+'=([^&#]*)').exec(url);if(result){return decodeURIComponent(result[1]);}
return null;};utils.setURLParam=function(paramName,paramValue,url){var regexp=new RegExp('([\\?&])('+utils.escapeRegexp(encodeURIComponent(paramName))+'=)[^&#]*');if(regexp.exec(url)===null){if(url.indexOf('?')==-1){url+='?';}else if(url.indexOf('?')<url.length-1){url+='&';}
url+=encodeURIComponent(paramName)+'='+encodeURIComponent(paramValue);return url;}else if(paramValue===null){return url.replace(regexp,'$1').replace(/\?\&/,'?').replace(/\&\&/,'&').replace(/[\?\&]$/,'');}else{return url.replace(regexp,'$1$2'+encodeURIComponent(paramValue));}};utils.parseUrlParams=function(url){var
result={},params=url.replace(/^[^?]*\?|\#.*$/g,'').split('&');for(var i=0;i<params.length;i++){var param=params[i].split('=');if(!param[0]){param[0]='';}
if(!param[1]){param[1]='';}
param[0]=decodeURIComponent(param[0]);param[1]=decodeURIComponent(param[1]);result[param[0]]=param[1];}
return result;};utils.escapeRegexp=function(string){return string.replace(/[-\/\\^$*+?.()|[\]{}]/g,'\\$&');};utils.generateUrl=function(routeName,params){var url=ENTER.config.pageConfig.routes[routeName]['pattern'];$.each((params||{}),function(paramName,paramValue){if(url.indexOf('{'+paramName+'}')!=-1){url=url.replace('{'+paramName+'}',paramValue);}else{var params={};params[paramName]=paramValue;url+=(url.indexOf('?')==-1?'?':'&')+$.param(params);}});return url;};utils.getObjectWithElement=function(array,elementKey,expectedElementValue){var object=null;if(array){$.each(array,function(arrayKey,arrayValue){if(arrayValue[elementKey]===expectedElementValue){object=arrayValue;return false;}});}
return object;};utils.validateEmail=function(email){var re=/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;return re.test(email);};utils.checkEan=function checkEanF(data){var ValidChars="0123456789",i,digit,originalCheck,even,odd,total,checksum,eanCode;eanCode=data.toString().replace(/\s+/g,'');for(i=0;i<eanCode.length;i++){digit=eanCode.charAt(i);if(ValidChars.indexOf(digit)==-1)return false;}
if(eanCode.length==8)eanCode="00000"+eanCode;else if(eanCode.length!=13)return false;originalCheck=eanCode.substring(eanCode.length-1);eanCode=eanCode.substring(0,eanCode.length-1);even=Number(eanCode.charAt(1))+
Number(eanCode.charAt(3))+
Number(eanCode.charAt(5))+
Number(eanCode.charAt(7))+
Number(eanCode.charAt(9))+
Number(eanCode.charAt(11));even*=3;odd=Number(eanCode.charAt(0))+
Number(eanCode.charAt(2))+
Number(eanCode.charAt(4))+
Number(eanCode.charAt(6))+
Number(eanCode.charAt(8))+
Number(eanCode.charAt(10));total=even+odd;checksum=total%10;if(checksum!=0){checksum=10-checksum;}
return checksum==originalCheck;};utils.arrayUnique=function(array){var unique=[];for(var i=0;i<array.length;i++){if(unique.indexOf(array[i])==-1){unique.push(array[i]);}}
return unique;};utils.Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=utils.Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=utils.Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
utils.sendOrderToGA=function(orderData){var	oData=orderData||{orders:[]};console.log('[Google Analytics] Start processing orders',oData.orders);var productUis=[];$.each(oData.orders,function(i,o){var googleOrderTrackingData={};googleOrderTrackingData.transaction={'id':o.numberErp,'affiliation':o.is_partner?'Партнер':'Enter','total':o.paySum,'shipping':o.delivery.price,'city':o.region.name};googleOrderTrackingData.products=$.map(o.products,function(p){var
productName=p.name,labels=[];if(o.isSlot){labels.push('marketplace-slot');}else if(o.is_partner){labels.push('marketplace');}
if(p.sender){labels.push(p.sender);}
if(p.sender&&p.position){labels.push('RR_'+p.position);}
if(p.sender2=='credit')labels.push('Credit');if(labels.length){productName+=' ('+labels.join(')(')+')';}
if(ENTER.config.pageConfig.selfDeliveryTest&&ENTER.config.pageConfig.selfDeliveryLimit>parseInt(o.paySum,10)-o.delivery.price)productName=productName+' (paid pickup)';if(p.sender){if(typeof p.sender=='string'&&p.sender.indexOf('filter')==0){$('body').trigger('trackGoogleEvent',{category:p.sender,action:'buy',label:p.isFromProductCard?'product':'basket'});}else{var rrEventLabel='';if(p.sender2=='slot'){rrEventLabel='_marketplace-slot';}else if(p.sender2=='marketplace'){rrEventLabel='_marketplace';}
if(p.from)$body.trigger('trackGoogleEvent',['RR_покупка'+rrEventLabel,'Купил просмотренные',p.position||'']);else $body.trigger('trackGoogleEvent',['RR_покупка'+rrEventLabel,'Купил добавленные',p.position||'']);}}
if(p.inCompare){(function(){var action;if(p.isSlot){action='marketplace-slot';}else if(p.isOnlyFromPartner){action='marketplace';}else{action='enter';}
$body.trigger('trackGoogleEvent',['Compare_покупка',action,p.compareLocation]);})();}
productUis.push(p.ui);return{'id':p.id,'name':productName,'sku':p.article,'category':p.category.length?(p.category[0].name+' - '+p.category[p.category.length-1].name):'','price':p.price,'quantity':p.quantity}});if(o.isCredit){if($.grep(o.products,function(product){return product.sender2=='credit'}).length>0){$body.trigger('trackGoogleEvent',['Credit','Покупка','Карточка товара'])}else{$body.trigger('trackGoogleEvent',['Credit','Покупка','Оформление заказа'])}}
console.log('[Google Analytics] Order',googleOrderTrackingData);$body.trigger('trackGoogleTransaction',[googleOrderTrackingData]);});(function(){var
action='',label='';var reviewProducts=ENTER.utils.analytics.reviews.get(productUis);console.log('localstorage',localStorage.getItem('enter.analytics.reviews'));console.log('products ui',productUis);console.log('reviewProducts',reviewProducts);if(reviewProducts.length){for(var i=0;i<reviewProducts.length;i++){action+=(i>0?'_':'')+(i+1)+'_All_'+reviewProducts[i].avgScore+'_Top_'+reviewProducts[i].firstPageAvgScore;label+=(i>0?'_':'')+(i+1)+'_'+reviewProducts[i].categoryName;}
$body.trigger('trackGoogleEvent',{category:'Items_review_transaction',action:action,label:label});}})();};utils.sendAdd2BasketGaEvent=function(productArticle,productPrice,isOnlyFromPartner,isSlot,senderName){if(productArticle){var location;if(ENTER.config.pageConfig.location.indexOf('listing')!=-1){location='listing';}else if(ENTER.config.pageConfig.location.indexOf('product')!=-1){location='product';}
if(location){var actions=[];if(senderName=='gift'){actions.push(location+'-gift');}
if(typeof productPrice!='undefined'&&parseInt(productPrice,10)<500){actions.push(location+'-500');}
if(isSlot){actions.push(location+'-marketplace-slot');}else if(isOnlyFromPartner){actions.push(location+'-marketplace');}else{actions.push(location);}
$body.trigger('trackGoogleEvent',['Add2Basket','('+actions.join(')(')+')',productArticle]);}}};utils.sendSortEvent=function(sort,category){if($('.js-slice').length){return;}
$('body').trigger('trackGoogleEvent',{category:'sort',action:sort,label:utils.getPageBusinessUnitId()+(function(){var result='';if(!category||!category.name){return[];}
if(category.ancestors){$.each(category.ancestors,function(key,category){result+='_'+category.name;});}
return result+'_'+category.name;})()});};utils.getPageBusinessUnitId=function(){return document.location.pathname.replace(/^(?:\/(?:catalog|product))?\/(slices?\/(?:[^\/]*\/)?[^\/]*|[^\/]*).*$/i,'$1');};var abstractAnalytics={add:function(storageName,itemData,storageMaxLength){if(!window.localStorage){return;}
try{var data=JSON.parse(localStorage.getItem(storageName))||[];}catch(e){data=[];}
for(var i=0;i<data.length;i++){if(data[i][0]==itemData[0]){data.splice(i,1);break;}}
data.push(itemData);while(JSON.stringify(data).length>storageMaxLength){data.shift();}
localStorage.setItem(storageName,JSON.stringify(data));},get:function(storageName,itemsIdentifiers){if(!window.localStorage){return;}
try{var data=JSON.parse(localStorage.getItem(storageName))||[];}catch(e){data=[];}
var result=[];for(var i=0;i<data.length;i++){if(itemsIdentifiers.indexOf(data[i][0])!=-1){result.push(data[i]);}}
return result;},clean:function(storageName){if(!window.localStorage){return;}
localStorage.removeItem(storageName);}};utils.analytics={isEnabled:function(){return typeof ga==='function'&&typeof ga.getAll=='function'&&ga.getAll().length!=0},addEcommData:function(action,elem,additionalData){var data=typeof elem.tagName!='undefined'?$(elem).data('ecommerce'):elem;if(!this.isEnabled||typeof data!='object')return;if(typeof additionalData!='undefined')data=$.extend({},data,additionalData);ga(action,data);},addImpression:function(elem,additionalData){this.addEcommData('ec:addImpression',elem,additionalData);},addProduct:function(elem,additionalData){this.addEcommData('ec:addProduct',elem,additionalData)},setAction:function(action,params){if(this.isEnabled())ga('ec:setAction',action,typeof params!=='undefined'?params:{})},reviews:{add:function(productUi,avgScore,firstPageAvgScore,categoryName){return abstractAnalytics.add('enter.analytics.reviews',[productUi,avgScore,firstPageAvgScore,categoryName],3000);},get:function(productUis){var result=abstractAnalytics.get('enter.analytics.reviews',productUis);for(var i=0;i<result.length;i++){result[i]={avgScore:result[i][1],firstPageAvgScore:result[i][2],categoryName:result[i][3]};}
return result;},clean:function(){return abstractAnalytics.clean('enter.analytics.reviews');}},productPageSenders:{add:function(productUi,sender){if(sender){return abstractAnalytics.add('enter.analytics.productPageSenders',[productUi,sender],2500);}},get:function($button){var sender=$button.data('sender')||{};if($('body').data('template')=='product_card'&&($button.data('location')=='product-card'||$button.data('location')=='userbar')){var
product=$('#jsProductCard').data('value')||{},productPageSender=abstractAnalytics.get('enter.analytics.productPageSenders',[product.ui]);productPageSender=(productPageSender[0]?productPageSender[0][1]:null)||product.oldProductPageSender;if(productPageSender&&typeof productPageSender=='object'){function isSenderPresent(sender){if(sender&&typeof sender=='object'){for(var key in sender){if(sender.hasOwnProperty(key)&&key!='from'&&sender[key]){return true;}}}
return false;}
if(!isSenderPresent(sender)){sender=productPageSender;}}
if(sender&&typeof sender.name=='string'&&sender.name.indexOf('filter')==0){sender.isFromProductCard=true;}}
return sender;},clean:function(){return abstractAnalytics.clean('enter.analytics.productPageSenders');}},productPageSenders2:{add:function(productUi,sender2){if(sender2){return abstractAnalytics.add('enter.analytics.productPageSenders2',[productUi,sender2],1000);}},get:function($button){var sender2=$button.data('sender2')||'';if($('body').data('template')=='product_card'&&($button.data('location')=='product-card'||$button.data('location')=='userbar')){var
product=$('#jsProductCard').data('value')||{},productPageSender2=abstractAnalytics.get('enter.analytics.productPageSenders2',[product.ui]);productPageSender2=(productPageSender2[0]?productPageSender2[0][1]:null)||product.oldProductPageSender2;if(productPageSender2&&!sender2){sender2=productPageSender2;}}
return sender2;},clean:function(){return abstractAnalytics.clean('enter.analytics.productPageSenders2');}}};}(window.ENTER));
//@ sourceMappingURL=library.js.map