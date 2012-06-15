<?php
/**
 * Symfony-WP proxy layer
 */
class SF_WP_Proxy
{
    private static $instance;

    private $content;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance()
    {
        if(empty(self::$instance))
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function init()
    {
        $standard_helpers = sfConfig::get('sf_standard_helpers');
        $standard_helpers = array_diff($standard_helpers, array('I18N'));
        sfConfig::set('sf_standard_helpers', $standard_helpers);

        define('WP_USE_THEMES', true);

        ob_start();
        require_once( 'wp-blog-header.php' );
        $this->content = ob_get_contents();
        if (function_exists('is_feed') && is_feed())
        {
            ob_end_flush();
            throw new sfStopException();
        }
        else
        {
            ob_end_clean();
        }
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getPermalink($id)
    {
        return get_permalink($id);
    }

    public function getPost()
    {
        global $post;

        return $post;
    }

    public function getSidebar()
    {
        ob_start();
        get_sidebar();
        $sidebarContent = ob_get_contents();
        ob_end_clean();

        return $sidebarContent;
    }

    public function getTitle()
    {
        return the_title('', '', False);
    }

    public function getPage($id)
    {
        return get_page($id);
    }

    public function getSideBarMenu($currentPage = Null)
    {
        ob_start();
        getSidebarMenu($currentPage);
        $sidebarMenuContent = ob_get_contents();
        ob_end_clean();

        return $sidebarMenuContent;
    }

    public function getCurrentLayout()
    {
        global $post;

        $terms = (array)wp_get_post_terms( $post->ID , 'layout', '');
        $term = Null;
        $layout = Null;
        if(!empty($terms))
        {
            $term = array_pop($terms);
        }

        if(is_object($term))
        {
            $layout = get_tax_meta($term->term_id,'layout_file_name');
        }

        return (string)$layout;
    }
}