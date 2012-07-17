<?php
$fck_browser_url = $this->plugin_path .'filemanager/browser/default/browser.html?Connector=../../connectors/php/connector.php';
$fck_upload_url = $this->plugin_path .'filemanager/connectors/php/upload.php';
$is_comment_form = (is_page() || is_single());
?>
height : '<?php echo (is_page() || is_single())? $this->comment_form_height : $this->EditorHeight;?>px',
skin : '<?php echo $this->skin;?>',
extraPlugins : 'wpmore',
disableNativeSpellChecker : <?php echo ($this->NativeSpellChecker ? 'false' : 'true');?>,
//colorButton_enableMore : true,
<?php if ($this->can_upload() && !$is_comment_form){?>
filebrowserBrowseUrl : '<?php echo $fck_browser_url; ?>',
filebrowserImageBrowseUrl : '<?php echo $fck_browser_url; ?>&Type=Image',
filebrowserFlashBrowseUrl : '<?php echo $fck_browser_url; ?>&Type=Flash',
filebrowserUploadUrl : '<?php echo $fck_upload_url;?>',
filebrowserImageUploadUrl : '<?php echo $fck_upload_url;?>?Type=Image',
filebrowserFlashUploadUrl : '<?php echo $fck_upload_url;?>?Type=Flash',
<?php } ?>
toolbar_MyToolBar :
[
	<?php echo ($is_comment_form ? $this->comment_toolbar : $this->post_toolbar);?>
],
toolbar : 'MyToolBar'
