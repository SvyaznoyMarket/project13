<?php

require_once(dirname(__FILE__).'/../../../wp-config.php');
class deans_fckeditor {
    var $version = '3.3.1';
    var $default_options = array();
	var $options = array();
	var $fckeditor_path = "";
	var $plugin_path ="";

    function deans_fckeditor()
	{
		$this->__construct();
	}

	function __construct()
    {
		$siteurl = trailingslashit(get_option('siteurl'));
		$this->plugin_path =  $siteurl .'wp-content/plugins/' . basename(dirname(__FILE__)) .'/';
		$this->fckeditor_path = $siteurl .'wp-content/plugins/' . basename(dirname(__FILE__)) .'/ckeditor/';
        $this->default_options['user_file_path'] = 'wp-content/uploads/';
        $this->default_options['EditorHeight'] = '300';
		$this->default_options['file_denied_ext'] = 'php,php2,php3,php4,php5,phtml,pwml,inc,asp,aspx,ascx,jsp,cfm,cfc,pl,bat,exe,com,dll,vbs,js,reg,cgi,htaccess';
		$this->default_options['image_allowed_ext'] = 'jpg,gif,jpeg,png';
		$this->default_options['flash_allowed_ext'] = 'swf,fla';
		$this->default_options['post_toolbar'] = "['Source','-','Save','NewPage','Preview','-','Templates'],
['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
'/',
['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
['Link','Unlink','Anchor'],
['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','-','wpMore'],
'/',
['Styles','Format','Font','FontSize'],
['TextColor','BGColor'],
['Maximize', 'ShowBlocks','-','About']";
		$this->default_options['comment_toolbar'] = "['Styles', 'Font','FontSize'],
['Bold','Italic','Strike'],['TextColor','BGColor']";
		$this->default_options['skin'] = 'kama';
		$this->default_options['NativeSpellChecker'] = true;
		$this->default_options['default_link_target'] = '';
		$this->default_options['p_breakBeforeOpen'] = true;
		$this->default_options['p_breakAfterOpen'] = false;
		$this->default_options['p_breakBeforeClose'] = false;
		$this->default_options['p_breakAfterClose'] = true;
		$this->default_options['p_indent'] = true;
		$this->default_options['comment_form'] = true;
		$this->default_options['comment_form_height'] = 120;
		$this->default_options['promote_me'] = true;

		$options = get_option('deans_ckeditor33');
		if (!$options) {
			add_option('deans_ckeditor33', $this->default_options);
			$options = $this->default_options;
		}
		$this->options = $options;
		foreach ($options as $option_name => $option_value)
	        $this-> {$option_name} = $option_value;
		if ($this->skin == 'default')
		{
			$this->skin = 'kama';
		}
		$path = str_replace(ABSPATH, '', trim($this->user_file_path));
		$dir = ABSPATH . $path;
		if ( $dir == ABSPATH ) { //the option was empty
			$dir = ABSPATH . 'wp-content/uploads';
		}
		$this->user_files_absolute_path = $dir;
		$this->user_files_url = $siteurl . $path;

    }

	function can_upload()
	{
		if ((function_exists('current_user_can') && current_user_can('upload_files')) || (isset($user_level) && $user_level >= 3))
		{
			return true;
		}
		return false;
	}

	function deactivate()
	{
		global $current_user;
		update_user_option($current_user->id, 'rich_editing', 'true', true);
	}

	function activate()
	{
		global $current_user;
		update_user_option($current_user->id, 'rich_editing', 'false', true);
		
	}

	function checkbox($var, $text, $onClick = '', $prefix='cb_') {
		return '<label id="lbl_' . $var . '"><input type="checkbox" id="cb_' . $var . '" name="' . $prefix. $var . '"' .
		($onClick != '' ? ' onClick="' . $onClick .'" ' : '') .
		($this->options[$var] ? "checked" : '') . '>&nbsp;' . wp__($text, 'pagebar') . "</label>\n";
	}


    function option_page()
    {
        $message = "";
        if (!empty($_POST['submit_update'])) {
			$new_options = array (
				'user_file_path' => trim($_POST['ch_str_UserFilesPath']),
				'EditorHeight' => trim($_POST['EditorHeight']),
				'file_denied_ext' =>trim($_POST['file_denied_ext']),
				'image_allowed_ext' =>trim($_POST['image_allowed_ext']),
				'flash_allowed_ext' =>trim($_POST['flash_allowed_ext']),
				'skin' =>trim($_POST['cmbSkins']),
				'NativeSpellChecker' => (bool)($_POST['cb_NativeSpellChecker']),
				'default_link_target' => trim($_POST['cmdefault_link_target']),
				'p_breakBeforeOpen' => (bool)$_POST['cb_p_breakBeforeOpen'],
				'p_breakAfterOpen' => (bool)$_POST['cb_p_breakAfterOpen'],
				'p_breakBeforeClose' => (bool)$_POST['cb_p_breakBeforeClose'],
				'p_breakAfterClose' => (bool)$_POST['cb_p_breakAfterClose'],
				'comment_form' =>(bool)$_POST['cb_comment_form'],
			    'comment_form_height' =>(int)$_POST['comment_form_height'],
				'promote_me' => (bool)$_POST['cb_promote_me'],
				'p_indent' =>(bool)$_POST['cb_p_indent'],
				'post_toolbar' =>stripslashes($_POST['post_toolbar']),
				'comment_toolbar' =>stripslashes($_POST['comment_toolbar'])
				);

			if (empty($new_options['user_file_path']))
			{
				$new_options['user_file_path'] = 'wp-content/uploads';
			}
			if ( ! ereg( '/$', $new_options["user_file_path"] ) )
				$new_options["user_file_path"] .= '/' ;
			
			update_option("deans_ckeditor33", $new_options);
			$this->options = $new_options;

			foreach ($new_options as $option_name => $option_value)
		        $this-> {$option_name} = $option_value;

			echo '<div class="updated"><p>' . wp__('Configuration updated!') . '</p></div>';
        }
		else if (isset($_POST['submit_reset'])) {
				update_option('deans_ckeditor33', $this->default_options);
				foreach ($this->default_options as $option_name => $option_value)
					$this-> {$option_name} = $option_value;
				$this->options = $this->default_options;
				echo '<div class="updated"><p>' . wp__('Configuration updated!') . '</p></div>';
		}
        ?>
		<div class=wrap>
		<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e('Dean\'s FCKEditor', 'deans_fckeditor') ?> <?php echo $this->version?>&nbsp;</h2>
<div style="float:right;"><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form></div>

		<form method="post" >
			<h3><?php _e('Common Options', 'deans_fckeditor') ?></h3>
			<table class="form-table">
			
			<tr valign="top">
<th scope="row"><?php _e('Select the skin to load')?></th><td>
<select name="cmbSkins"">
				<option value="kama"  <?php if ($this->skin == 'kama') { ?> selected="selected"<?php } ?>>Default</option>
				<option value="office2003" <?php if ($this->skin == 'office2003') { ?> selected="selected"<?php } ?>>Office 2003</option>
				<option value="v2" <?php if ($this->skin == 'v2') { ?> selected="selected"<?php } ?>>v2</option>
			</select></td></tr>
			<tr valign="top">
<th scope="row"><?php _e('built-in spell checker')?></th><td>
<?php echo $this->checkbox('NativeSpellChecker', 'Enable the built-in spell checker while typing natively available in the browser.(currently Firefox and Safari only)');?></td></tr>
<tr><th scope="row"><?php _e('Output formatting(Writer Rules)')?></th>
<td>
<?php echo $this->checkbox('p_indent', 'indent the element contents.');?><br />
<?php echo $this->checkbox('p_breakBeforeOpen', 'break line before the opener tag.');?><br />
<?php echo $this->checkbox('p_breakAfterOpen', 'break line after the opener tag.');?><br />
<?php echo $this->checkbox('p_breakBeforeClose', 'break line before the closer tag.');?><br />
<?php echo $this->checkbox('p_breakAfterClose', 'break line after the closer tag.');?><br />
</td></tr>
</table>

<h3>Post/Page Editor options</h3>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Editor height')?></th><td><input type="text" name="EditorHeight"  value="<?php echo $this->EditorHeight;
        ?>"/>px</td></tr>
		<tr valign="top">
<th scope="row"><?php _e('Set the toolbar buttons')?></th><td>
<textarea name="post_toolbar" id="post_toolbar" class="large-text code" rows="12"><?php echo $this->post_toolbar;?></textarea>
</td></tr>

</table>

<h3>Comment Editor Options</h3>
<table class="form-table">
<tr valign="top"><th scope="row"><?php _e('Use CKeditor')?></th>
<td><?php echo $this->checkbox('comment_form', 'Use CKeditor as comment editor');?></td></tr>
<tr valign="top"><th scope="row"><?php _e('Editor height for comment')?></th>
<td><input type="text" name="comment_form_height" value="<?php echo htmlentities($this->comment_form_height);?>"/>px</td></tr>
	<tr valign="top">
<th scope="row"><?php _e('Set the toolbar buttons')?></th><td>
<textarea name="comment_toolbar" id="comment_toolbar" class="large-text code" rows="3"><?php echo $this->comment_toolbar;?></textarea>
</td></tr>
<tr valign="top"><th scope="row"><?php _e('Help promote this Plugin')?></th>
<td><?php echo $this->checkbox('promote_me', 'Help promote this Plugin');?><span class="description">This option will add 'This visual editor brought to you by fckeditor for wordpress plugin' on the bottom of your comment form</span></td></tr>

</table>
			<h3><?php _e('Upload Options', 'deans_fckeditor') ?></h3>
			<table class="form-table">
<tr valign="top"><th scope="row"><?php _e('Store uploads in this folder')?></th>
<td><input type="text" class="regular-text" name="ch_str_UserFilesPath" value="<?php echo htmlentities($this->user_file_path);?>"/><span class="description"><?php _e('Default is')?> wp-content/uploads</span></td></tr>
<tr valign="top"><th scope="row">Denied file extension</th><td><input type="text" style="width: 60%;" size="50" name="file_denied_ext" value="<?php echo htmlentities($this->file_denied_ext);?>"/></td></tr>
<tr valign="top"><th scope="row">Allowed image extension</th><td><input type="text" class="regular-text" name="image_allowed_ext" value="<?php echo htmlentities($this->image_allowed_ext);?>"/></td></tr>
<tr valign="top"><th scope="row">Allowed flash extension</th><td><input type="text" class="regular-text" size="50" name="flash_allowed_ext" value="<?php echo htmlentities($this->flash_allowed_ext);?>"/></td></tr>
</table>
			<p class="submit">
				<input type="hidden" name="df_submit" value="1" />
				<input type="submit" value="Reset to defaults" name="submit_reset" class="button-secondary" id="default-reset" />
				<input type="submit" class="button-primary" value="Update Options" name="submit_update" />
				</p>
		<h3><?php _e('Informations and support', 'deans_fckeditor')?></h3>				
		<p><?php echo str_replace("%s", "<a href=\"http://www.deanlee.cn/wordpress/fckeditor-for-wordpress-plugin/\">http://www.deanlee.cn/wordpress/fckeditor-for-wordpress-plugin/</a>", wp__("Check %s for updates and comment there if you have any problems / questions / suggestions.", 'deans_fckeditor'));
        ?></p>
		</form></div>
		<?php
    }

    function add_admin_head()
    {
    ?>
		<style type="text/css">
			#quicktags { display: none; }
		</style>
	<?php
    }

	function _load_script($textarea_id)
	{
		?>
		<script type="text/javascript">
		//<![CDATA[
		function _deans_fckeditor_load(){
			CKEDITOR.replace('<?php echo $textarea_id;?>',
				{
				<?php eval('?>'.file_get_contents(dirname(__FILE__).'/custom_config_js.php')) ?>
				});

		CKEDITOR.on( 'instanceReady', function( ev )
		{
			var dtd = CKEDITOR.dtd;
			for ( var e in CKEDITOR.tools.extend( {}, dtd.$block, dtd.$listItem, dtd.$tableContent ) )
			{
				ev.editor.dataProcessor.writer.setRules( e,
					{
						indent : <?php echo $this->p_indent ? 'true' : 'false'; ?>,
						breakBeforeOpen : <?php echo $this->p_breakBeforeOpen ? 'true' : 'false'; ?>,
						breakAfterOpen : <?php echo $this->p_breakAfterOpen ? 'true' : 'false'; ?>,
						breakBeforeClose : <?php echo $this->p_breakBeforeClose ? 'true' : 'false'; ?>,//!dtd[ e ][ '#' ],
						breakAfterClose : <?php echo $this->p_breakAfterClose ? 'true' : 'false'; ?>
					});
			}
			ev.editor.dataProcessor.writer.setRules( 'br',
				{
					breakAfterOpen : true
				});
			ev.editor.dataProcessor.writer.setRules( 'pre',
				{
				  indent: false
				});
		});
		}
		_deans_fckeditor_load();
		//]]>
		</script>
		<?PHP
	}

	function load_fckeditor()
	{
		
		$this->_load_script('content');
		
	}

		function load_comment_form()
	{
			if (!(is_page() || is_single()))
				return;
			$this->_load_script('comment');
		?>
	<script type="text/javascript">
		//<![CDATA[
		function _ckeditor_updateelement()
		{
			CKEDITOR.instances.comment.updateElement();
		}
		var submitbtn = document.getElementById('submit');
		if (submitbtn)
		{
			submitbtn.onclick = _ckeditor_updateelement;
		}
		<?php if ($this->promote_me){?>
			var container = document.getElementById('comment').parentNode;
			if (container)
			{
				var insert = document.createElement('div');
				insert.id = 'promote_ckeditor';
				insert.innerHTML = 'This visual editor brought to you by <a href="http://www.deanlee.cn/wordpress/fckeditor-for-wordpress-plugin/" target="_blank">fckeditor for wordpress plugin</a>';
				container.appendChild(insert);
			}
		<?php } ?>
	//]]>
	</script>
		<?php
	}

    function user_personalopts_update()
    {
        global $current_user;
        update_user_option($current_user->id, 'rich_editing', 'false', true);
    }

    function add_option_page()
    {
		add_options_page('FCKEditor', 'FCKEditor', 8, 'deans_fckeditor', array(&$this, 'option_page'));
		/*add_submenu_page('post.php', 'FCKEditor','FCKEditor',1, 'deans_fckeditor', array(&$this, 'option_page'));*/
    }

	function add_admin_js()
	{
		wp_deregister_script(array('media-upload')); 
		wp_enqueue_script('media-upload', $this->plugin_path .'media-upload.js', array('thickbox'), '20080710'); 
		wp_enqueue_script('fckeditor', $this->fckeditor_path . 'ckeditor.js');
	}

	function add_comment_js()
	{
		if (is_page() || is_single())
			wp_enqueue_script('fckeditor', $this->fckeditor_path . 'ckeditor.js');
	}
	
	function init_filter()
	{
		
		global $allowedtags;
		$allowedtags = array(
		'a' => array(
			'href' => array (),
			'title' => array ()),
		'abbr' => array(
			'title' => array ()),
		'acronym' => array(
			'title' => array ()),
		'b' => array(),
		'blockquote' => array(
			'cite' => array ()),
		//	'br' => array(),
		'cite' => array (),
		'code' => array(),
		'del' => array(
			'datetime' => array ()),
		//	'dd' => array(),
		//	'dl' => array(),
		//	'dt' => array(),
		'em' => array (), 'i' => array (),
		//	'ins' => array('datetime' => array(), 'cite' => array()),
		//	'li' => array(),
		//	'ol' => array(),
		//	'p' => array(),
		'q' => array(
			'cite' => array ()),
		'strike' => array(),
		'strong' => array(),
		'font' => array(
			'color' => array (),
			'face' => array (),
			'size' => array ()),
		'span' =>array(
			'style'=>array()),
		//	'sub' => array(),
		//	'sup' => array(),
		//	'u' => array(),
		//	'ul' => array(),
	);
	}

}

$deans_fckeditor = new deans_fckeditor();
?>