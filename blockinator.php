<?php
/*
Plugin Name: Blockinator
Plugin URI: https://imgood.nl/blockinator/
Description: This plugin will remove script and version numbers from the source of your pages.
Author: stoffijn
Author URI: https://stoffijn.nl
Tags: remove, xmlrpc, security, version number
Version: 1.0.0
License: GPLv2 or later.
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Blockinator {
    
    public $options;
    public function __construct() {
        $this->options = get_option('blockinator_options');
        $this->blockinator_register_settings_and_fields();
    }

    public function blockinator_add_menu_page() {
        add_options_page('Blockinator', 'Blockinator', 'administrator', __FILE__, array('Blockinator','blockinator_display_options_page'));
    }

    public static function blockinator_display_options_page() {
        ?>
        <div class="wrap">
            <h3> Blockinator Settings </h3>
            <form method="post" action="options.php">
                
                <?php 
                settings_fields('blockinator_options');
                do_settings_sections(__FILE__);
                ?>
                
                <p class="submit">
                    <input name="submit" type="submit" class="button-primary" value="Save Changes" />
                </p>
            </form>
        </div>
        <?php
    }

    public function blockinator_register_settings_and_fields() {
        register_setting('blockinator_options', 'blockinator_options');
        add_settings_section('xmlrpc_disable_section', '<span style="text-decoration: underline;">Disable script</span> <span style="font-size: 70%; color:#666666;"> (This disables the function)</span>', array($this, 'blkntr_xmlrpc_disable_callback'), __FILE__);
        add_settings_field('xmlrpc_disable_enable_checkbox', 'Disable XMLRPC', array($this, 'blkntr_xmlrpc_disable_checkbox'), __FILE__, 'xmlrpc_disable_section');
        add_settings_section('remove_from_source', '<span style="text-decoration: underline;">Remove from source</span> <span style="font-size: 70%; color:#666666;"> (This removes the script from the html code of your pages)</span>', array($this, 'blkntr_remove_from_source_callback'), __FILE__);
        add_settings_field('xmlrpc_disable_headers_checkbox', 'Remove XMLRPC from headers', array($this, 'blkntr_xmlrpc_disable_headers_checkbox'), __FILE__, 'remove_from_source');
        add_settings_field('remove_version_numbers_checkbox', 'Remove version numbers', array($this, 'blkntr_remove_version_numbers_checkbox'), __FILE__, 'remove_from_source');
        add_settings_field('remove_shortlinks_checkbox', 'Remove shortlinks', array($this, 'blkntr_remove_shortlinks_checkbox'), __FILE__, 'remove_from_source');
        add_settings_field('remove_feedslinks_checkbox', 'Remove feeds links', array($this, 'blkntr_remove_feedslinks_checkbox'), __FILE__, 'remove_from_source');
        add_settings_field('remove_dns_prefetch_checkbox', 'Remove dns prefetch', array($this, 'blkntr_remove_dns_prefetch_checkbox'), __FILE__, 'remove_from_source');
    }
    
    public function blkntr_xmlrpc_disable_callback() {
        // no callback as of now
    }

    public function blkntr_remove_from_source_callback() {
        // no callback as of now
    }
    
    public function blkntr_xmlrpc_disable_checkbox() {
        ?>
        <input name="blockinator_options[xmlrpc_disable_enable_checkbox]" type="checkbox" value="1"
        <?php if(isset($this->options['xmlrpc_disable_enable_checkbox']) && $this->options['xmlrpc_disable_enable_checkbox'] == 1){echo 'checked';} ?> />
        <?php 
    }

    public function blkntr_xmlrpc_disable_headers_checkbox() {
        ?>
        <input name="blockinator_options[xmlrpc_disable_headers_checkbox]" type="checkbox" value="1"<?php 
        if(isset($this->options['xmlrpc_disable_headers_checkbox']) && $this->options['xmlrpc_disable_headers_checkbox'] == 1){echo 'checked';} ?> />
        <?php
    }

    public function blkntr_remove_version_numbers_checkbox() {
        ?>
        <input name="blockinator_options[remove_version_numbers_checkbox]" type="checkbox" value="1"<?php 
        if(isset($this->options['remove_version_numbers_checkbox']) && $this->options['remove_version_numbers_checkbox'] == 1){echo 'checked';} ?> />
        <?php
    }

        public function blkntr_remove_shortlinks_checkbox() {
        ?>
        <input name="blockinator_options[remove_shortlinks_checkbox]" type="checkbox" value="1"<?php 
        if(isset($this->options['remove_shortlinks_checkbox']) && $this->options['remove_shortlinks_checkbox'] == 1){echo 'checked';} ?> />
        <?php
    }

    public function blkntr_remove_feedslinks_checkbox() {
        ?>
        <input name="blockinator_options[remove_feedslinks_checkbox]" type="checkbox" value="1"<?php 
        if(isset($this->options['remove_feedslinks_checkbox']) && $this->options['remove_feedslinks_checkbox'] == 1){echo 'checked';} ?> />
        <?php
    }

    public function blkntr_remove_dns_prefetch_checkbox() {
        ?>
        <input name="blockinator_options[remove_dns_prefetch_checkbox]" type="checkbox" value="1"<?php 
        if(isset($this->options['remove_dns_prefetch_checkbox']) && $this->options['remove_dns_prefetch_checkbox'] == 1){echo 'checked';} ?> />
        <?php
    }
}

/**
* Retrieve all option settings for this plugin
**/
$options = get_option('blockinator_options');

/**
 * Hook into the generator.
 */
if( isset($options['xmlrpc_disable_enable_checkbox']) && ($options['xmlrpc_disable_enable_checkbox'] == 1) ) {
    add_filter('xmlrpc_enabled', '__return_false');
}

/**
* Check if enabled then remove version numbers from script
**/
if( isset($options['remove_version_numbers_checkbox']) && ($options['remove_version_numbers_checkbox'] == 1) ) {
   // Remove WordPress Meta Generator
    remove_action('wp_head', 'wp_generator');
    add_filter( 'style_loader_src', 'blockinator_hide_wordpress_version', 10, 2 );
    add_filter( 'script_loader_src', 'blockinator_hide_wordpress_version', 10, 2 );
    add_filter('the_generator', 'blkntr_hide_wordpress_version');
}

/**
* Check if enabled then remove shortlinks from script
**/
if( isset($options['remove_shortlinks_checkbox']) && ($options['remove_shortlinks_checkbox'] == 1) ) {
   // Remove Shortlinks
    remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
}

/**
* Check if enabled then remove feedslinks from script
**/
if( isset($options['remove_feedslinks_checkbox']) && ($options['remove_feedslinks_checkbox'] == 1) ) {
   // Remove feedslinks
    remove_action( 'wp_head', 'feed_links_extra', 3 );
    remove_action( 'wp_head', 'feed_links', 2 );
}

/**
* Check if enabled then remove dns-prefetch from script
**/
if( isset($options['remove_dns_prefetch_checkbox']) && ($options['remove_dns_prefetch_checkbox'] == 1) ) {
   // Remove dns-prefetch
    remove_action( 'wp_head', 'wp_resource_hints', 2 );
}

/**
 * Hook into the script loader and remove the version information.
 */
if( isset($options['xmlrpc_disable_headers_checkbox']) && ($options['xmlrpc_disable_headers_checkbox'] == 1) ) {
    /** REMOVE XMLRPC FROM HEADERS **/
    add_filter('wp_headers', function($headers, $wp_query){
        if (array_key_exists('X-Pingback', $headers)) {
            unset($headers['X-Pingback']);
        }
        return $headers;
    }, 11, 2);

    /** Remove xmlrpc pingback from headers **/
    add_filter('bloginfo_url', function($output, $property){
        error_log("====property=" . $property);
        return ($property == 'pingback_url') ? null : $output;
    }, 11, 2);

    /** 
    *Remove xmlrpc edituri from headers
    */
    add_action('wp', function(){
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links' );
    }, 11);
}

add_action('admin_menu', 'blkntr_add_options_page');

// Hide WordPress Version Info
function blkntr_hide_wordpress_version() {
  return '';
}

// Remove WordPress Version Number In URL Parameters From JS/CSS
function blockinator_hide_wordpress_version($src, $handle) {
    $src = remove_query_arg(array('ver','version'), $src);
    return $src;
}

function blkntr_add_options_page() {
    $object = new Blockinator();
    $object->blockinator_add_menu_page();
}

add_action('admin_init', 'blockinator_initiate_class');

function blockinator_initiate_class() {
    new Blockinator();
}

function blockinator_defaults() {
    $current_options = get_option('blockinator_options');
    
    $defaults = array(
        'xmlrpc_disable_enable_checkbox'            => 0,
        'xmlrpc_disable_headers_checkbox'           => 0,
        'remove_version_numbers_checkbox'           => 0,
        'remove_shortlinks_checkbox'                => 0,
        'remove_feedslinks_checkbox'                => 0,
        'remove_dns_prefetch'                       => 0
    );
    
    if( is_admin() ) {
        update_option( 'blockinator_options', $defaults );
    }
}

register_activation_hook( __FILE__, 'blockinator_defaults' );

function blockinator_set_plugin_meta($links, $file) {
    
    $plugin = plugin_basename(__FILE__);
 
    // create link
    if ($file == $plugin) {
        return array_merge(
            $links,
            array( sprintf( '<a href="options-general.php?page=%s">%s</a>', $plugin, __('Settings') ) )
        );
    }
 
    return $links;
}
if ( is_network_admin() ) {

}
else{
    add_filter( 'plugin_row_meta', 'blockinator_set_plugin_meta', 10, 2 );
}
/**
*remove wp version param from any enqueued scripts
*/
function blockinator_remove_wp_ver_css_js( $src ) {
    if ( strpos( $src, 'ver=' ) )
        $src = remove_query_arg( 'ver', $src );
    return $src;
}
?>
