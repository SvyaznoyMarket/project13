<?php
/**
 * Template Name: Шаблон без меню
 * Description: A Page Template that exclude a sidebar to pages
 *
 * @package WordPress & Enter
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', 'page' ); ?>
<?php endwhile; // end of the loop. ?>
<?php get_footer(); ?>