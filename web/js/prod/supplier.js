
;(function($,window,document,undefined)
{var pluginName="fileUpload",defaults={uploadData:{},submitData:{},uploadOptions:{},submitOptions:{},before:function(){},beforeSubmit:function(){return true;},success:function(){},error:function(){},complete:function(){}};function Plugin(element,options)
{this.element=element;this.$form=$(element);this.$uploaders=$('input[type=file]',this.element);this.files={};this.settings=$.extend({},defaults,options);this._defaults=defaults;this._name=pluginName;this.init();}
Plugin.prototype={init:function()
{this.$uploaders.on('change',{context:this},this.processFiles);this.$form.on('submit',{context:this},this.uploadFiles);},processFiles:function(event)
{var self=event.data.context;self.files[$(event.target).attr('name')]=event.target.files;},uploadFiles:function(event)
{event.stopPropagation();event.preventDefault();var self=event.data.context;self.settings.before();var data=new FormData();data.append('file_upload_incoming','1');$.each(self.files,function(key,field)
{$.each(field,function(key,value)
{data.append(key,value);});});$.each(self.settings.uploadData,function(key,value)
{data.append(key,value);});$.ajax($.extend({},{url:self.$form.attr('action'),type:'POST',data:data,cache:false,dataType:'json',processData:false,contentType:false,success:function(data,textStatus,jqXHR){self.processSubmit(event,data);},error:function(jqXHR,textStatus,errorThrown){self.settings.error(jqXHR,textStatus,errorThrown);}},self.settings.uploadOptions));},processSubmit:function(event,uploadData)
{var self=event.data.context;if(!self.settings.beforeSubmit(uploadData))return;var data=self.$form.serializeArray();$.each(uploadData,function(key,value)
{data.push({'name':key,'value':value});});$.each(self.settings.submitData,function(key,value)
{data.push({'name':key,'value':value});});$.ajax($.extend({},{url:self.$form.attr('action'),type:'POST',data:data,cache:false,dataType:'json',success:function(data,textStatus,jqXHR){self.settings.success(data,textStatus,jqXHR);},error:function(jqXHR,textStatus,errorThrown){self.settings.error(jqXHR,textStatus,errorThrown);},complete:function(jqXHR,textStatus){self.settings.complete(jqXHR,textStatus);}},self.settings.submitOptions));}};$.fn[pluginName]=function(options)
{return this.each(function()
{if(!$.data(this,"plugin_"+pluginName))
{$.data(this,"plugin_"+pluginName,new Plugin(this,options));}});};})(jQuery,window,document);
+function($){var $supplierLoginButton=$('.jsSupplierLoginButton'),$authPopup=$('#auth-block'),authClass='supplier-login';console.log($);$supplierLoginButton.on('click',function(){$authPopup.addClass(authClass);$authPopup.lightbox_me({onClose:function(){$authPopup.removeClass(authClass)}})})}(jQuery);
+function(){var $fileForm=$('#priceForm'),$detailsForm=$('#detailsForm'),$fileInput=$('#priceInput'),$fileButton=$('#priceButton');$fileForm.fileUpload();$fileButton.on('click',function(e){e.preventDefault();$fileInput.click();});$fileInput.on('change',function(){$fileForm.submit();});$detailsForm.on('submit',function(e){e.preventDefault();$.ajax($detailsForm.attr('action'),{type:'POST',data:$detailsForm.serialize(),success:function(data){console.log(data);},error:function(){console.error('User update failed')}})})}();
//@ sourceMappingURL=supplier.js.map