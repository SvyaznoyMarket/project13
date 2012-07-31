<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php /*
        <h1 class="entry-title"><?php the_title(); ?></h1>
	    */ ?>
    </header><!-- .entry-header -->

	<div class="entry-content">
        <?php
            ob_start();
            the_content();
            $content = ob_get_contents();
            ob_end_clean();
            $content = str_replace('%credit_program_widget%', credit_program_widget(), $content);
            $content = str_replace('%bank_description_list_widget%', bank_description_list_widget(), $content);
            $content = str_replace('%region_delivery_widget%', region_delivery_widget(), $content);
        ?>

		<?php echo $content; ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . wp__( 'Pages:', 'twentyeleven' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<footer class="entry-meta">
		<?php /*edit_post_link( wp__( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' );*/ ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-<?php the_ID(); ?> -->
