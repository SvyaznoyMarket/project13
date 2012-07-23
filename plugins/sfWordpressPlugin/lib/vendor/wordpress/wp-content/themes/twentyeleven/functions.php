<?php
/**
 * Twenty Eleven functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twentyeleven_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;

/**
 * Tell WordPress to run twentyeleven_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'twentyeleven_setup' );

if ( ! function_exists( 'twentyeleven_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twentyeleven_setup() in a child theme, add your own twentyeleven_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, and Post Formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_setup() {

	/* Make Twenty Eleven available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty Eleven, use a find and replace
	 * to change 'twentyeleven' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'twentyeleven', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );

	// Grab Twenty Eleven's Ephemera widget.
	require( get_template_directory() . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', wp__( 'Primary Menu', 'twentyeleven' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	// Add support for custom backgrounds
	add_custom_background();

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// The next four constants set how Twenty Eleven supports custom headers.

	// The default header text color
	define( 'HEADER_TEXTCOLOR', '000' );

	// By leaving empty, we allow for random image rotation.
	define( 'HEADER_IMAGE', '' );

	// The height and width of your custom header.
	// Add a filter to twentyeleven_header_image_width and twentyeleven_header_image_height to change these values.
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyeleven_header_image_width', 1000 ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyeleven_header_image_height', 288 ) );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the size of the header image that we just defined
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );

	// Add Twenty Eleven's custom image sizes
	add_image_size( 'large-feature', HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true ); // Used for large feature (header) images
	add_image_size( 'small-feature', 500, 300 ); // Used for featured posts if a large-feature doesn't exist

	// Turn on random header image rotation by default.
	add_theme_support( 'custom-header', array( 'random-default' => true ) );

	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See twentyeleven_admin_header_style(), below.
	add_custom_image_header( 'twentyeleven_header_style', 'twentyeleven_admin_header_style', 'twentyeleven_admin_header_image' );

	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'wheel' => array(
			'url' => '%s/images/headers/wheel.jpg',
			'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Wheel', 'twentyeleven' )
		),
		'shore' => array(
			'url' => '%s/images/headers/shore.jpg',
			'thumbnail_url' => '%s/images/headers/shore-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Shore', 'twentyeleven' )
		),
		'trolley' => array(
			'url' => '%s/images/headers/trolley.jpg',
			'thumbnail_url' => '%s/images/headers/trolley-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Trolley', 'twentyeleven' )
		),
		'pine-cone' => array(
			'url' => '%s/images/headers/pine-cone.jpg',
			'thumbnail_url' => '%s/images/headers/pine-cone-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Pine Cone', 'twentyeleven' )
		),
		'chessboard' => array(
			'url' => '%s/images/headers/chessboard.jpg',
			'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Chessboard', 'twentyeleven' )
		),
		'lanterns' => array(
			'url' => '%s/images/headers/lanterns.jpg',
			'thumbnail_url' => '%s/images/headers/lanterns-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Lanterns', 'twentyeleven' )
		),
		'willow' => array(
			'url' => '%s/images/headers/willow.jpg',
			'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Willow', 'twentyeleven' )
		),
		'hanoi' => array(
			'url' => '%s/images/headers/hanoi.jpg',
			'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
			/* translators: header image description */
			'description' => wp__( 'Hanoi Plant', 'twentyeleven' )
		)
	) );
}
endif; // twentyeleven_setup

if ( ! function_exists( 'twentyeleven_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // twentyeleven_header_style

if ( ! function_exists( 'twentyeleven_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyeleven_setup().
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 32px;
		line-height: 36px;
		text-decoration: none;
	}
	#desc {
		font-size: 14px;
		line-height: 23px;
		padding: 0 0 3em;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 1000px;
		height: auto;
		width: 100%;
	}
	</style>
<?php
}
endif; // twentyeleven_admin_header_style

if ( ! function_exists( 'twentyeleven_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in twentyeleven_setup().
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_admin_header_image() { ?>
	<div id="headimg">
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // twentyeleven_admin_header_image

/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function twentyeleven_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twentyeleven_excerpt_length' );

/**
 * Returns a "Continue Reading" link for excerpts
 */
function twentyeleven_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . wp__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) . '</a>';
}

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and twentyeleven_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function twentyeleven_auto_excerpt_more( $more ) {
	return ' &hellip;' . twentyeleven_continue_reading_link();
}
add_filter( 'excerpt_more', 'twentyeleven_auto_excerpt_more' );

/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function twentyeleven_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twentyeleven_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twentyeleven_custom_excerpt_more' );

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function twentyeleven_page_menu_args( $args ) {
	$args['show_home'] = false;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twentyeleven_page_menu_args' );

/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_widgets_init() {

	register_widget( 'Twenty_Eleven_Ephemera_Widget' );

	register_sidebar( array(
		'name' => wp__( 'Main Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => wp__( 'Showcase Sidebar', 'twentyeleven' ),
		'id' => 'sidebar-2',
		'description' => wp__( 'The sidebar for the optional Showcase Template', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => wp__( 'Footer Area One', 'twentyeleven' ),
		'id' => 'sidebar-3',
		'description' => wp__( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => wp__( 'Footer Area Two', 'twentyeleven' ),
		'id' => 'sidebar-4',
		'description' => wp__( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => wp__( 'Footer Area Three', 'twentyeleven' ),
		'id' => 'sidebar-5',
		'description' => wp__( 'An optional widget area for your site footer', 'twentyeleven' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'twentyeleven_widgets_init' );

if ( ! function_exists( 'twentyeleven_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable
 */
function twentyeleven_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentyeleven' ); ?></h3>
			<div class="nav-previous"><?php next_posts_link( wp__( '<span class="meta-nav">&larr;</span> Older posts', 'twentyeleven' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( wp__( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyeleven' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}
endif; // twentyeleven_content_nav

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty Eleven 1.0
 * @return string|bool URL or false when no link is present.
 */
function twentyeleven_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function twentyeleven_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'twentyeleven_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twentyeleven_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( wp__( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( wp__( '%1$s on %2$s <span class="says">said:</span>', 'twentyeleven' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time pubdate datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( wp__( '%1$s at %2$s', 'twentyeleven' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( wp__( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twentyeleven' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => wp__( 'Reply <span>&darr;</span>', 'twentyeleven' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twentyeleven_comment()

if ( ! function_exists( 'twentyeleven_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own twentyeleven_posted_on to override in a child theme
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_posted_on() {
	printf( wp__( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( wp__( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Twenty Eleven 1.0
 */
function twentyeleven_body_classes( $classes ) {

	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'twentyeleven_body_classes' );


register_nav_menus(array(
    'left_top' => 'Верхнее левое меню',
    'left_middle' => 'Среднее левое меню',
    'left_bottom' => 'Нижнее левое меню'
));

add_editor_style('../../../../css/skin/inner.css');
add_editor_style('../../../../css/font.css');
add_editor_style('../../../../css/navy.css');

function getSidebarMenu($currentPage = Null)
{
    wp_nav_menu(array('theme_location'=>'left_top', 'menu_class' => 'leftmenu pb20', 'currentPage' => $currentPage));
    wp_nav_menu(array('theme_location'=>'left_middle', 'menu_class' => 'leftmenu pb20', 'currentPage' => $currentPage));
    wp_nav_menu(array('theme_location'=>'left_bottom', 'menu_class' => 'leftmenu pb20', 'currentPage' => $currentPage));
}

remove_filter('the_content', 'wpautop');


/*
 ********************************************************
 */

add_action( 'init', 'create_program' );
function create_program() {
    $labels = array(
        'name' => _x('Кредитные программы', 'post type general name'),
        'singular_name' => _x('Кредитная программа', 'post type singular name'),
        'add_new' => _x('Добавить новую кредитную программу', 'Кредитную программу'),
        'add_new_item' => wp__('Добавление новой кредитной программы'),
        'edit_item' => wp__('Изменить кредитную программу'),
        'new_item' => wp__('Новая кредитная программа'),
        'view_item' => wp__('Посмотреть кредитную программу'),
        'search_items' => wp__('Искать кредитные программы'),
        'not_found' =>  wp__('Кредитные программы не найдены'),
        'not_found_in_trash' => wp__('Кредитные программы не найдены в корзине'),
        'parent_item_colon' => ''
    );

    $supports = array(
        'title',
        'editor',
        #'custom-fields',
        #'revisions',
        #'excerpt',
        'post-thumbnails',
    );
    register_post_type( 'program',
        array(
            'labels' => $labels,
            'public' => true,
            'supports' => $supports,
            'show_ui' => true,
        )
    );

    /*
    $labels = array(
        'name' => _x( 'Категории', 'taxonomy general name' ),
        'singular_name' => _x( 'Категория', 'taxonomy singular name' ),
        'search_items' =>  wp__( 'Искать категории' ),
        'all_items' => wp__( 'Все категории' ),
        'parent_item' => wp__( 'Родительская категория' ),
        'parent_item_colon' => wp__( 'Родительская категория:' ),
        'edit_item' => wp__( 'Изменить категорию' ),
        'update_item' => wp__( 'Обновить категорию' ),
        'add_new_item' => wp__( 'Добавить новую категорию' ),
        'new_item_name' => wp__( 'Название новой категории' ),
        'menu_name' => wp__( 'Категория' ),
    );

    register_taxonomy('category', 'program', array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'category' ),
    ));*/

}


/*
 *************** Adding new taxonomy***********************
 */
add_action( 'init', 'create_bank_taxonomy' );

function create_bank_taxonomy() {
    $labels = array(
        'name' => _x( 'Банки', 'taxonomy general name' ),
        'singular_name' => _x( 'Банк', 'taxonomy singular name' ),
        'search_items' =>  wp__( 'Искать банки' ),
        'all_items' => wp__( 'Все банки' ),
        #'parent_item' => wp__( 'Родительская категория' ),
        #'parent_item_colon' => wp__( 'Родительская категория:' ),
        'edit_item' => wp__( 'Изменить банк' ),
        'update_item' => wp__( 'Обновить банк' ),
        'add_new_item' => wp__( 'Добавить новый банк' ),
        'new_item_name' => wp__( 'Название нового банка' ),
        'menu_name' => wp__( 'Банк' ),
    );

    #if (!taxonomy_exists('category')) {
        register_taxonomy( 'bank', 'program',
            array(
                'hierarchical' => false,
                'labels' => $labels,
                'query_var' => 'bank',
                'rewrite' => array( 'slug' => 'bank' ),
                'show_ui' => true
            )
        );
    #}
}

function add_bank_box() {
    remove_meta_box('tagsdiv-bank','program','core');
    add_meta_box('bank_box_ID', wp__('Банк'), 'bank_taxonomy_style_function', 'program', 'side', 'core');
}

function add_bank_menus() {

    if ( ! is_admin() )
        return;

    add_action('admin_menu', 'add_bank_box');
    add_action('save_post', 'save_bank_taxonomy_data');
}

add_bank_menus();

function bank_taxonomy_style_function($program) {

    echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' .
        wp_create_nonce( 'taxonomy_bank' ) . '" />';


    // Get all bank taxonomy terms
    $bankList = get_terms('bank', 'hide_empty=0');

    ?>
<select name='post_bank' id='post_bank'>
    <!-- Display bank list as options -->
    <?php
    $names = wp_get_object_terms($program->ID, 'bank');
    ?>
    <option class='bank-option' value=''
        <?php if (!count($names)) echo "selected";?>>Отсутствует</option>
    <?php
    foreach ($bankList as $bank) {
        if (!is_wp_error($names) && !empty($names) && !strcmp($bank->slug, $names[0]->slug))
            echo "<option class='theme-option' value='" . $bank->slug . "' selected>" . $bank->name . "</option>\n";
        else
            echo "<option class='theme-option' value='" . $bank->slug . "'>" . $bank->name . "</option>\n";
    }
    ?>
</select>
<?php
}

function save_bank_taxonomy_data($program_id) {
    // verify this came from our screen and with proper authorization.

    if ( !wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_bank' )) {
        return $program_id;
    }

    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $program_id;


    // Check permissions
    /*if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }*/

    // OK, we're authenticated: we need to find and save the data
    $program = get_page($program_id);


    if (($program->post_type == 'program')) {
        $bank = $_POST['post_bank'];
        wp_set_object_terms( $program_id, $bank, 'bank' );
    }
    return $bank;

}

// Add to admin_init function
add_filter("manage_edit-bank_columns", 'bank_columns');

function bank_columns($bank_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => wp__('Название'),
        'short_description' => wp__('Условия кредитования'),
        'logo' => wp__('Логотип'),
        'priority' => wp__('Приоритет')
		#'description' => wp__('Текст пункта выпадающего списка'),
        #'slug' => wp__('Slug'),
        #'posts' => wp__('Posts')
    );
    return $new_columns;
}

add_filter("manage_bank_custom_column", 'manage_bank_columns', 10, 3);

function manage_bank_columns($out, $column_name, $term_id) {
    $term = get_term($term_id, 'bank');
    switch ($column_name) {
        case 'logo':
            $logo = get_tax_meta($term_id,'image_field_id');
            $out .= "<img src=\"{$logo['src']}\" />";
            break;
        case 'short_description':
            $out .= mb_substr($term->description, 0, 100);
            if(mb_strlen($term->description) > 100) {
                $out .= '...';
            }
            break;
        case 'priority':
            $out .= $term->priority;
        default:
            break;
    }
    return $out;
}

/*
 ******************Category taxonomy*****************
 */

add_action( 'init', 'create_category_taxonomy' );

function create_category_taxonomy() {
    $labels = array(
        'name' => _x( 'Категории', 'taxonomy general name' ),
        'singular_name' => _x( 'Категория', 'taxonomy singular name' ),
        'search_items' =>  wp__( 'Искать категории' ),
        'all_items' => wp__( 'Все категории' ),
        #'parent_item' => wp__( 'Родительская категория' ),
        #'parent_item_colon' => wp__( 'Родительская категория:' ),
        'edit_item' => wp__( 'Изменить категорию' ),
        'update_item' => wp__( 'Обновить категорию' ),
        'add_new_item' => wp__( 'Добавить новую категорию' ),
        'new_item_name' => wp__( 'Название новой категории' ),
        'menu_name' => wp__( 'Категория' ),
    );

    register_taxonomy( 'category', 'program',
        array(
            'hierarchical' => true,
            'labels' => $labels,
            'query_var' => 'category',
            'rewrite' => array( 'slug' => 'category' ),
            'show_ui' => true
        )
    );
}

// Add to admin_init function
add_filter("manage_edit-category_columns", 'category_columns');

function category_columns($category_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => wp__('Название'),
        #'logo' => '',
        'description' => wp__('Текст пункта выпадающего списка'),
        'priority' => wp__('Приоритет'),
        #'slug' => wp__('Slug'),
        #'posts' => wp__('Posts')
    );
    return $new_columns;
}

add_filter("manage_category_custom_column", 'manage_category_columns', 10, 3);

function manage_category_columns($out, $column_name, $term_id) {
    $term = get_term($term_id, 'category');
    switch ($column_name) {
        case 'priority':
            $out .= $term->priority;
            break;
        default:
            break;
    }
    return $out;
}


/*
 ******************Meta tax*******************
 */

require_once( ABSPATH . '/wp-includes/Tax-meta-class/Tax-meta-class.php' );

$bankMetaBoxConfig = array(
    'id' => 'bank_logo_meta_box',                         // meta box id, unique per meta box
    'title' => 'Логотип',                      // meta box title
    'pages' => array('bank'),                    // taxonomy name, accept categories, post_tag and custom taxonomies
    'context' => 'normal',                           // where the meta box appear: normal (default), advanced, side; optional
    'fields' => array(),                             // list of meta fields (can be added by field arrays)
    'local_images' => false,                         // Use local or hosted images (meta box images for add/remove)
    'use_with_theme' => false                        //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
);

$bank_logo_meta_box = new Tax_Meta_Class($bankMetaBoxConfig);
$bank_logo_meta_box->addImage('image_field_id',array('name'=> 'Логотип'));
$bank_logo_meta_box->Finish();

$categoryMetaBoxConfig = array(
    'id' => 'category_meta_box',
    'title' => 'Ссылка',
    'pages' => array('category'),
    'context' => 'normal'
);

$bank_logo_meta_box = new Tax_Meta_Class($categoryMetaBoxConfig);
$bank_logo_meta_box->addText('category_url',array('name'=> 'Ссылка на раздел'));
$bank_logo_meta_box->Finish();


/*
 ******************Credit programs widget*******
 */
function credit_program_widget()
{
    ob_start();

    global $wpdb;

    $sql = "
        select tt.term_taxonomy_id as taxonomy_id, tt.description as taxonomy_description, p.post_content as program_description, tb.name as bank_name, tb.term_id as bank_id, t.name as category_name
        from wp_term_taxonomy tt
        inner join wp_terms t on t.term_id = tt.term_id
        left join wp_term_relationships tr on tr.term_taxonomy_id = tt.term_taxonomy_id
        left join wp_posts p on p.ID = tr.object_id and p.post_type = 'program' and p.post_status = 'publish'
        inner join wp_term_taxonomy ttb on ttb.taxonomy = 'bank'
        inner join wp_term_relationships trb on ttb.term_taxonomy_id = trb.term_taxonomy_id and trb.object_id = p.ID
        inner join wp_terms tb on tb.term_id = ttb.term_id
        where tt.taxonomy = 'category'
        order by tt.priority, ttb.priority;
    ";
    $creditProgramList = $wpdb->get_results($sql, ARRAY_A);

    $categoryList = array();
    foreach($creditProgramList as $program)
    {
        if(!isset($categoryList[$program['taxonomy_id']]))
        {
            $categoryList[$program['taxonomy_id']] = array(
                'taxonomy_description' => $program['taxonomy_description'],
                'category_name' => $program['category_name'],
                'category_url' => get_category_url($program['taxonomy_id']),
                'program_list' => array(
                    array(
                        'program_description' => $program['program_description'],
                        'bank_name' => $program['bank_name'],
                        'bank_image' => get_bank_image($program['bank_id'])
                    )
                )
            );
        }
        else
        {
            $categoryList[$program['taxonomy_id']]['program_list'][] = array(
                'program_description' => $program['program_description'],
                'bank_name' => $program['bank_name'],
                'bank_image' => get_bank_image($program['bank_id']),
            );
        }
    }


    #wp_enqueue_script( 'credit_program_widget', get_home_url() . '/wp-includes/js/jquery/jquery.js', null, null, true );
?>
    <select name="category_list" id="category_list">
        <option value="">Что вы будете брать в кредит</option>
        <?php foreach($categoryList as $categoryId => $categoryData) { ?>
            <option value="<?php echo $categoryId?>"><?php echo $categoryData['taxonomy_description']?></option>
        <?php } ?>
    </select>
    <div id="category_program_list"></div>

        <script src="/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
            var categoryList = <?php echo json_encode($categoryList)?>;

            var categorySelector = document.getElementById('category_list');
            categorySelector.onchange = function() {
                var taxonomyId = categorySelector.value;
                var programList = categoryList[taxonomyId]['program_list'];
                var programListContent = '';
                for(var i = 0; i < programList.length; i++)
                {
                    programListContent += '<dl><dt>'
                    programListContent += programList[i]['bank_image'] + '</dt>'
                    programListContent += '<dd><div class="line"></div><h3>' + programList[i]['bank_name'] + '</h3> <br />' + programList[i]['program_description'];
                    programListContent += '</dd></dl>';
                }

                programListContent += '<p class="ac mb25"><a class="bBigOrangeButton" href="' + categoryList[taxonomyId]['category_url'] + '">Перейти в раздел «' + categoryList[taxonomyId]['category_name'] + '»</a></p>';

                document.getElementById('category_program_list').innerHTML = programListContent;

                hideShortUL();
            };

            function hideShortUL()
            {
                var ulList = jQuery('ul[class="short"]');
                $.each(ulList, function(index, ul) {
                    var ulElements = $(ul).children();
                    if(ulElements.length > 4)
                    {
                        visULElements(ul, 'none');
                        addShowLink(ul);
                    }
                });
            }

            function addShowLink(ul)
            {
                var layer = $(ul).next();
                var aElement = '<a href=="#" onclick="return false;" name="ul_show_other_link">Подробнее</a>';
                $(ul).after(aElement);
            }

            jQuery('a[name="ul_show_other_link"]').live('click', function() {
                var ul = $(this).prev();
                visULElements(ul, '');
                $(this).css('display', 'none');
            });

            function visULElements(ul, display)
            {

                var elements = $(ul).children();

                $.each(elements, function(index, element) {
                   if(index >= 4)
                   {
                       $(element).css('display', display);
                   }
                });
            }

    </script>
    <?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}

function get_bank_image($bankId)
{
    $imgSrc = get_tax_meta($bankId,'image_field_id');

    return '<img src="' . $imgSrc['src'] . '" />';
}

function get_category_url($categoryId)
{
    $categoryUrl = get_tax_meta($categoryId, 'category_url');

    return $categoryUrl;
}

function bank_description_list_widget()
{
    ob_start();

    global $wpdb;

    $sql = "
        select t.name as bank_name, tt.description as bank_description
        from wp_term_taxonomy tt
        inner join wp_terms t on t.term_id = tt.term_id
        where tt.taxonomy = 'bank';
    ";
    $bankDescriptionList = $wpdb->get_results($sql, ARRAY_A);
?>
<ul class='bCreditLine2' style="display: none;">
    <?php foreach($bankDescriptionList as $bankDescriptionData) { ?>
        <li><i><?php echo $bankDescriptionData['bank_name']?>.</i> <?php echo $bankDescriptionData['bank_description']?></li>
    <?php } ?>
    <br />* обязательно наличие регистрации в регионе оформления кредита.
</ul>
<?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}




    add_action( 'init', 'create_layout_taxonomy' );

    function create_layout_taxonomy() {
        $labels = array(
            'name' => _x( 'Шаблон SF', 'taxonomy layout name' ),
            'singular_name' => _x( 'Шаблон SF', 'taxonomy singular name' ),
            'search_items' =>  wp__( 'Искать шаблоны SF' ),
            'all_items' => wp__( 'Все шаблоны SF' ),
            #'parent_item' => wp__( 'Родительская категория' ),
            #'parent_item_colon' => wp__( 'Родительская категория:' ),
            'edit_item' => wp__( 'Изменить шаблон SF' ),
            'update_item' => wp__( 'Обновить шаблон SF' ),
            'add_new_item' => wp__( 'Добавить новый шаблон SF' ),
            'new_item_name' => wp__( 'Название нового шаблона SF' ),
            'menu_name' => wp__( 'Шаблон SF' ),
        );

        register_taxonomy( 'layout', 'page',
            array(
                'hierarchical' => false,
                'labels' => $labels,
                'query_var' => 'layout',
                'rewrite' => array( 'slug' => 'layout' ),
                'show_ui' => false
            )
        );
    }

    function add_layout_box() {
        remove_meta_box('tagsdiv-layout','page','core');
        add_meta_box('layout_box_ID', wp__('Шаблон SF'), 'layout_taxonomy_style_function', 'page', 'side', 'core');
    }

    function add_layout_menus() {

        if ( ! is_admin() )
            return;

        add_action('admin_menu', 'add_layout_box');
        add_action('save_post', 'save_layout_taxonomy_data');
    }

    add_layout_menus();

    function layout_taxonomy_style_function($page) {

        echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' .
            wp_create_nonce( 'taxonomy_layout' ) . '" />';


        // Get all layout taxonomy terms
        $layoutList = get_terms('layout', 'hide_empty=0');

        ?>
    <select name='post_layout' id='post_layout'>
        <!-- Display layout list as options -->
        <?php
        $names = wp_get_object_terms($page->ID, 'layout');
        ?>
        <option class='layout-option' value=''
            <?php if (!count($names)) echo "selected";?>>Стандартный</option>
        <?php
        foreach ($layoutList as $layout) {
            if (!is_wp_error($names) && !empty($names) && !strcmp($layout->slug, $names[0]->slug))
                echo "<option class='theme-option' value='" . $layout->slug . "' selected>" . $layout->name . "</option>\n";
            else
                echo "<option class='theme-option' value='" . $layout->slug . "'>" . $layout->name . "</option>\n";
        }
        ?>
    </select>
    <?php
    }

    function save_layout_taxonomy_data($page_id) {
        // verify this came from our screen and with proper authorization.

        if ( !wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_layout' )) {
            return $page_id;
        }

        // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
            return $page_id;


        // Check permissions
        /*if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) )
                return $post_id;
        } else {
            if ( !current_user_can( 'edit_post', $post_id ) )
                return $post_id;
        }*/

        // OK, we're authenticated: we need to find and save the data
        $page = get_page($page_id);


        if (($page->post_type == 'page')) {
            $layout = $_POST['post_layout'];
            wp_set_object_terms( $page_id, $layout, 'layout' );
        }
        return $layout;

    }

$layoutMetaBoxConfig = array(
    'id' => 'layout_meta_box',
    'title' => 'Название файла',
    'pages' => array('layout'),
    'context' => 'normal'
);

$layout_logo_meta_box = new Tax_Meta_Class($layoutMetaBoxConfig);
$layout_logo_meta_box->addText('layout_file_name',array('name'=> 'Название файла шаблона'));
$layout_logo_meta_box->Finish();



/**
 * Условия доставки
 */

add_action( 'init', 'create_delivery_option' );
function create_delivery_option() {
    $labels = array(
        'name' => _x('Условия доставки', 'post type general name'),
        'singular_name' => _x('Условие доставки', 'post type singular name'),
        'add_new' => _x('Добавить новое условие доставки', 'Условие доставки'),
        'add_new_item' => wp__('Добавление нового условия доставки'),
        'edit_item' => wp__('Изменить условие доставки'),
        'new_item' => wp__('Новое условие доставки'),
        'view_item' => wp__('Посмотреть условие доставки'),
        'search_items' => wp__('Искать условия доставки'),
        'not_found' =>  wp__('Условия доставки не найдены'),
        'not_found_in_trash' => wp__('Условия доставки не найдены в корзине'),
        'parent_item_colon' => ''
    );

    $supports = array(
        'title',
        'editor',
        #'custom-fields',
        #'revisions',
        #'excerpt',
        'post-thumbnails',
    );
    register_post_type( 'delivery',
        array(
            'labels' => $labels,
            'public' => true,
            'supports' => $supports,
            'show_ui' => true,
        )
    );
}

add_action( 'init', 'create_region_taxonomy' );

function create_region_taxonomy() {
    $labels = array(
        'name' => _x( 'Регионы', 'taxonomy general name' ),
        'singular_name' => _x( 'Регион', 'taxonomy singular name' ),
        'search_items' =>  wp__( 'Искать регионы' ),
        'all_items' => wp__( 'Все регионы' ),
        #'parent_item' => wp__( 'Родительская категория' ),
        #'parent_item_colon' => wp__( 'Родительская категория:' ),
        'edit_item' => wp__( 'Изменить регион' ),
        'update_item' => wp__( 'Обновить регион' ),
        'add_new_item' => wp__( 'Добавить новый регион' ),
        'new_item_name' => wp__( 'Название нового региона' ),
        'menu_name' => wp__( 'Регион' ),
    );

    register_taxonomy( 'region', 'delivery',
        array(
            'hierarchical' => false,
            'labels' => $labels,
            'query_var' => 'region',
            'rewrite' => array( 'slug' => 'region' ),
            'show_ui' => true
        )
    );
}

function add_region_box() {
    remove_meta_box('tagsdiv-region','delivery','core');
    add_meta_box('region_box_ID', wp__('Регион'), 'region_taxonomy_style_function', 'delivery', 'side', 'core');
}

function add_region_menus() {

    if ( ! is_admin() )
        return;

    add_action('admin_menu', 'add_region_box');
    add_action('save_post', 'save_region_taxonomy_data');
}

add_region_menus();

function region_taxonomy_style_function($program) {

    echo '<input type="hidden" name="taxonomy_noncename" id="taxonomy_noncename" value="' .
        wp_create_nonce( 'taxonomy_region' ) . '" />';


    // Get all region taxonomy terms
    $regionList = get_terms('region', 'hide_empty=0');

    ?>
<select name='post_region' id='post_region'>
    <!-- Display bank list as options -->
    <?php
    $names = wp_get_object_terms($program->ID, 'region');
    ?>
    <option class='region-option' value=''
        <?php if (!count($names)) echo "selected";?>>Отсутствует</option>
    <?php
    foreach ($regionList as $region) {
        if (!is_wp_error($names) && !empty($names) && !strcmp($region->slug, $names[0]->slug))
            echo "<option class='theme-option' value='" . $region->slug . "' selected>" . $region->name . "</option>\n";
        else
            echo "<option class='theme-option' value='" . $region->slug . "'>" . $region->name . "</option>\n";
    }
    ?>
</select>
<?php
}

function save_region_taxonomy_data($program_id) {
    // verify this came from our screen and with proper authorization.

    if ( !wp_verify_nonce( $_POST['taxonomy_noncename'], 'taxonomy_region' )) {
        return $program_id;
    }

    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $program_id;


    // Check permissions
    /*if ( 'page' == $_POST['post_type'] ) {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return $post_id;
    } else {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return $post_id;
    }*/

    // OK, we're authenticated: we need to find and save the data
    $program = get_page($program_id);


    if (($program->post_type == 'delivery')) {
        $region = $_POST['post_region'];
        wp_set_object_terms( $program_id, $region, 'region' );
    }
    return $region;

}

// Add to admin_init function
add_filter("manage_edit-region_columns", 'region_columns');

function region_columns($region_columns) {
    $new_columns = array(
        'cb' => '<input type="checkbox" />',
        'name' => wp__('Название'),
        #'short_description' => wp__('Условия кредитования'),
        #'logo' => wp__('Логотип'),
        #'priority' => wp__('Приоритет')
        #'description' => wp__('Текст пункта выпадающего списка'),
        #'slug' => wp__('Slug'),
        #'posts' => wp__('Posts')
    );
    return $new_columns;
}

/*add_filter("manage_bank_custom_column", 'manage_bank_columns', 10, 3);

function manage_bank_columns($out, $column_name, $term_id) {
    $term = get_term($term_id, 'bank');
    switch ($column_name) {
        case 'logo':
            $logo = get_tax_meta($term_id,'image_field_id');
            $out .= "<img src=\"{$logo['src']}\" />";
            break;
        case 'short_description':
            $out .= mb_substr($term->description, 0, 100);
            if(mb_strlen($term->description) > 100) {
                $out .= '...';
            }
            break;
        case 'priority':
            $out .= $term->priority;
        default:
            break;
    }
    return $out;
}*/

/*
 ******************Region delivery widget*******
 */
function region_delivery_widget()
{
    ob_start();

    global $wpdb;

    $sql = "
        select
            tt.term_taxonomy_id as taxonomy_id,
            tt.description as taxonomy_description,
            p.post_content as delivery_option,
            tb.name as region_name,
            tb.term_id as region_id
        from wp_term_taxonomy tt
        inner join wp_terms t on t.term_id = tt.term_id
        left join wp_term_relationships tr on tr.term_taxonomy_id = tt.term_taxonomy_id
        left join wp_posts p on p.ID = tr.object_id and p.post_type = 'delivery' and p.post_status = 'publish'
        inner join wp_term_taxonomy ttb on ttb.taxonomy = 'region'
        inner join wp_term_relationships trb on ttb.term_taxonomy_id = trb.term_taxonomy_id and trb.object_id = p.ID
        inner join wp_terms tb on tb.term_id = ttb.term_id
        #where tt.taxonomy = 'category'
        order by tt.priority, ttb.priority;
    ";
    $regionList = $wpdb->get_results($sql, ARRAY_A);

    ?>
<select name="region_list" id="region_list">
    <?php foreach($regionList as $regionId => $regionData) { ?>
    <option value="<?php echo $regionId?>" <?=$regionId == 0?'selected':Null?>><?php echo $regionData['region_name']?></option>
    <?php } ?>
</select>
<br />
<br />
<div id="region_delivery_option">
    <?=$regionList[0]['delivery_option']?>
</div>

<script src="/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
<script type="text/javascript">
    var regionList = <?php echo json_encode($regionList) ?>;

    var regionSelector = document.getElementById('region_list');
    regionSelector.onchange = function() {
        var taxonomyId = regionSelector.value;
        document.getElementById('region_delivery_option').innerHTML = regionList[taxonomyId]['delivery_option'];
    };

</script>
<?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
}