<?php
/**
 * Plugin Name: Wisor Events Plugin
 * Description: A custom WordPress plugin to display events using Elementor and shortcodes.
 * Version: 1.0
 * Author: Austin Edache Abah
 * Author URI: @adswithed
 * Text Domain: wisor-events-plugin
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin path
define('WISOR_EVENTS_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include necessary files
require_once WISOR_EVENTS_PLUGIN_PATH . 'includes/class-events-cpt.php';
require_once WISOR_EVENTS_PLUGIN_PATH . 'includes/class-elementor-widget.php';
require_once WISOR_EVENTS_PLUGIN_PATH . 'includes/class-shortcode.php';
require_once WISOR_EVENTS_PLUGIN_PATH . 'includes/class-plugin-settings.php';

// Flush rewrite rules on activation to fix "page not found" issue
function wisor_events_flush_rewrite_rules() {
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'wisor_events_flush_rewrite_rules');

function wisor_events_enqueue_assets() {
    // Generate security nonce
    $security_nonce = wp_create_nonce('wisor_load_more_nonce');

    // Enqueue CSS
    wp_enqueue_style('wisor-events-style', plugin_dir_url(__FILE__) . 'assets/css/event-widget-style.css');

    // Enqueue JavaScript with jQuery dependency
    wp_enqueue_script('wisor-events-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);

    // Pass AJAX URL and security nonce to JavaScript
    wp_localize_script('wisor-events-script', 'wisor_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => $security_nonce,
    ));

    // Debug the nonce in PHP
    error_log("Generated Security Nonce: " . $security_nonce);
}
add_action('wp_enqueue_scripts', 'wisor_events_enqueue_assets');



// Initialize the plugin
function wisor_events_plugin_init() {
    new Wisor_Events_CPT();  // Initializes custom post type
}
add_action('plugins_loaded', 'wisor_events_plugin_init');



//Ajax load more event handling
add_action('wp_ajax_wisor_load_more_events', 'wisor_load_more_events');
add_action('wp_ajax_nopriv_wisor_load_more_events', 'wisor_load_more_events');

function wisor_load_more_events() {
    // Debug: Log the AJAX request
    error_log("AJAX Request Received: " . print_r($_POST, true));

    // Verify nonce for security
    if (!isset($_POST['security']) || !wp_verify_nonce($_POST['security'], 'wisor_load_more_nonce')) {
        error_log("Security check failed!");
        wp_send_json_error(['message' => 'Security check failed!']);
        die();
    }

    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
    $layout = isset($_POST['layout']) ? sanitize_text_field($_POST['layout']) : 'list';
    $events_per_page = isset($_POST['events_per_page']) ? intval($_POST['events_per_page']) : 6;

    if ($paged < 1 || $events_per_page < 1) {
        error_log("Invalid page or events_per_page value.");
        wp_send_json_error(['message' => 'Invalid page or events per page value.']);
        die();
    }

    $query = new WP_Query([
        'post_type'      => 'events',
        'posts_per_page' => $events_per_page,
        'paged'          => $paged,
        'meta_key'       => '_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
    ]);

    if ($query->have_posts()) {
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();

            // Fix the template file path
            // $template_file = plugin_dir_path(__FILE__) . 'template-parts/event-' . $layout . '.php';
            $template_file = plugin_dir_path(__FILE__) . 'template-parts/' . ($layout_class === 'wisor-events-grid' ? 'event-grid.php' : 'event-list.php');

            if (file_exists($template_file)) {
                include $template_file;
            } else {
                error_log("Template file missing: " . $template_file);
                wp_send_json_error(['message' => 'Template file missing: ' . $template_file]);
                die();
            }

        }
        wp_reset_postdata();
        wp_send_json_success(['html' => ob_get_clean()]);
    } else {
        error_log("No more events found.");
        wp_send_json_error(['message' => 'No more events found.']);
    }
}



?>
