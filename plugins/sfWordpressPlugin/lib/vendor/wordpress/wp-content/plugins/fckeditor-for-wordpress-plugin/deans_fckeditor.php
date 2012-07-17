<?php
/*
Plugin Name: Dean's FCKEditor For Wordpress
Plugin URI: http://www.deanlee.cn/wordpress/fckeditor-for-wordpress-plugin/
Description: Replaces the default Wordpress editor with <a href="http://ckeditor.com//"> CKeditor</a>
Version: 3.3.1
Author: Dean Lee
Author URI: http://www.deanlee.cn/
*/
require_once('deans_fckeditor_class.php');
add_action('admin_menu', array(&$deans_fckeditor, 'add_option_page'));
add_action('admin_head', array(&$deans_fckeditor, 'add_admin_head'));
add_action('personal_options_update', array(&$deans_fckeditor, 'user_personalopts_update'));
add_action('edit_form_advanced', array(&$deans_fckeditor, 'load_fckeditor'));
add_action('edit_page_form', array(&$deans_fckeditor, 'load_fckeditor'));
add_action('simple_edit_form', array(&$deans_fckeditor, 'load_fckeditor'));
add_action('admin_print_scripts', array(&$deans_fckeditor, 'add_admin_js'));
if ($deans_fckeditor->comment_form){
	add_action('init', array(&$deans_fckeditor, 'init_filter'));
	add_action('wp_print_scripts', array(&$deans_fckeditor, 'add_comment_js'));
	add_action('comment_form', array(&$deans_fckeditor, 'load_comment_form'));
	//remove_filter('pre_comment_content', 'wp_filter_kses');
	//add_filter('pre_comment_content', array(&$deans_fckeditor, 'pre_comment_content'));
}
register_activation_hook(basename(dirname(__FILE__)).'/' . basename(__FILE__), array(&$deans_fckeditor, 'activate'));
register_deactivation_hook(basename(dirname(__FILE__)).'/' . basename(__FILE__), array(&$deans_fckeditor, 'deactivate'));
?>