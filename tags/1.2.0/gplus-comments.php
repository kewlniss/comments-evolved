<?php
/*
Plugin Name: Google+ Comments
Plugin URI: http://www.cloudhero.net/gplus-comments
Description: Google+ Comments for WordPress plugin adds Google Plus comments along side your native WordPress comment system in a responsive tab interface.
Author: Brandon Holtsclaw <me@brandonholtsclaw.com>
Author URI: http://www.brandonholtsclaw.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Donate link: http://www.wepay.com/donations/brandonholtsclaw
Version: 1.2.0
*/

/* * *
 * * *     !! WORDPRESS DEVELOPERS AND THEMERS : PLEASE READ BEFORE YOU EDIT THIS FILE.
 * * *
 * * *     DO NOT EDIT THIS FILE DIRECTLY, IF YOU WANT TO CUSTOMIZE THE THEME MARKUP
 * * *     COPY ANY OF THE FILES IN wp-content/plugins/gplus-comments/templates/*.php
 * * *     TO YOUR CURRENT WORDPRESS THEME DIRECTORY WITH THE SAME FILENAME, IF FOUND IN THE
 * * *     THEME IT IS USED BY THE PLUGIN INSTEAD OF ITS OWN TEMPLATE MARKUP.
 * * *
 */

function gplus_comments_init()
{
  define( 'GPLUS_COMMENTS_VERSION', '1.1.5' );
  defined('GPLUS_COMMENTS_DEBUG') or define('GPLUS_COMMENTS_DEBUG', false);
  defined('GPLUS_COMMENTS_DIR') or define('GPLUS_COMMENTS_DIR', dirname(__FILE__));
  defined('GPLUS_COMMENTS_URL') or define('GPLUS_COMMENTS_URL', rtrim(plugin_dir_url(__FILE__),"/"));
  defined('GPLUS_COMMENTS_LIB') or define('GPLUS_COMMENTS_LIB', GPLUS_COMMENTS_DIR . "/lib");
  defined('GPLUS_COMMENTS_TEMPLATES') or define('GPLUS_COMMENTS_TEMPLATES', GPLUS_COMMENTS_DIR . "/templates");

  wp_register_style('gplus_comments_font', GPLUS_COMMENTS_URL . '/font/font.css', null, GPLUS_COMMENTS_VERSION, "all");
  wp_register_style('gplus_comments_tabs_css', GPLUS_COMMENTS_URL . '/styles/tabs.css', array("gplus_comments_font"), GPLUS_COMMENTS_VERSION, "screen");
  wp_register_script('gplus_comments_tabs_js', GPLUS_COMMENTS_URL . '/js/tabs.js', null, GPLUS_COMMENTS_VERSION, true);
}
add_action('init', 'gplus_comments_init');

function gplus_comments_admin_init()
{
  register_setting( 'gplus-comments-options', 'gplus-comments' );
}
add_action( 'admin_init', 'gplus_comments_admin_init' );

register_activation_hook( __FILE__, function() {
  $options = array();
  $options = get_option('gplus-comments');
  $options["show_fb"] = 1;
  $options["show_wp"] = 1;
  $options["show_disqus"] = 0;
  $options["show_trackbacks"] = 0;
  update_option('gplus-comments', $options);
});

/**
 * Replace the theme's loaded comments.php with our own souped up version.
 */
function gplus_comments_template($file)
{
    global $post, $comments;

    /**
     * Do we even need to load ?
     */
    if (!(is_singular() && (have_comments() || 'open' == $post->comment_status))) { return; }

    /**
     * This will allow theme authors to override the comments template files easy.
     */
    if (file_exists(TEMPLATEPATH . '/comments-container.php'))
    {
      return TEMPLATEPATH . '/comments-container.php';
    }
    else
    {
      return GPLUS_COMMENTS_TEMPLATES . '/comments-container.php';
    }
}
add_filter('comments_template', 'gplus_comments_template');
//add_filter('get_comments_number', 'gplus_comments_get_comments_number');

/**
 * Load up our assets for frontend to make us pretty and functional.
 */
function gplus_comments_load_assets()
{
  wp_enqueue_style('gplus_comments_font');
  wp_enqueue_style('gplus_comments_tabs_css');
  wp_enqueue_script('gplus_comments_tabs_js');
}
add_action('wp_head', 'gplus_comments_load_assets');

/**
 * Set the link for settings under the plugin name on the wp-admin plugins page
 */
function gplus_comments_plugin_action_links($links, $file) {
  $plugin_file = basename(__FILE__);
  if (basename($file) == $plugin_file) {
    $settings_link = '<a href="edit-comments.php?page=gplus-comments">Settings</a>';
    array_unshift($links, $settings_link);
  }
  return $links;
}
add_filter('plugin_action_links', 'gplus_comments_plugin_action_links', 10, 2);

/**
 * Load the G+ options page when called by the admin_menu
 */
function gplus_comments_render_admin_page()
{
  include_once(GPLUS_COMMENTS_LIB . '/gplus-comments-admin.php');
}

/**
 * Add ourself to the admin menu under the Comments section
 */
function gplus_comments_admin_menu()
{
     add_submenu_page
     (
         'edit-comments.php',
         'Google+ Comments',
         'G+ Comments',
         'manage_options',
         'gplus-comments',
         'gplus_comments_render_admin_page'
     );
}
add_action('admin_menu', 'gplus_comments_admin_menu', 10);

/**
 * Load a bit of jQuery to adjust some of the minor admin menu items
 */
function gplus_comments_admin_head()
{
  print "<script type='text/javascript'>jQuery(document).ready(function($) { $('ul.wp-submenu a[href=\"edit-comments.php\"]').text('WP Comments'); $('#menu-comments').find('a.wp-has-submenu').attr('href', 'edit-comments.php?page=gplus-comments').end().find('.wp-submenu  li:has(a[href=\"edit-comments.php?page=gplus-comments\"])').prependTo($('#menu-comments').find('.wp-submenu ul')); $('#wp-admin-bar-comments a.ab-item').attr('href', 'edit-comments.php?page=gplus-comments'); });</script>";
}
add_action('admin_head', 'gplus_comments_admin_head');
