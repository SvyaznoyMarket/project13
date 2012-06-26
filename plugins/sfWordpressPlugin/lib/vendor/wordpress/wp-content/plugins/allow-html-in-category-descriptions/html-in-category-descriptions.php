<?php
/*
Plugin Name: HTML in Category Descriptions
Version: 1.1
Plugin URI: 
Description: Allows you to add HTML code in category descriptions
Author: Arno Esterhuizen
Author URI: arno.esterhuizen@gmail.com
*/

$filters = array('pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description');
foreach ( $filters as $filter ) {
	remove_filter($filter, 'wp_filter_kses');
}

foreach ( array( 'term_description' ) as $filter ) {
	remove_filter( $filter, 'wp_kses_data' );
}
?>