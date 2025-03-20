<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Events_CPT {
    public function __construct() {
        add_action('init', array($this, 'register_events_cpt'));
        add_action('add_meta_boxes', array($this, 'add_event_date_meta_box'));
        add_action('save_post', array($this, 'save_event_date'));
        add_action('save_post_events', function() {
            global $wpdb;
            // Delete all cached data related to events when an event is updated
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wisor_events_cache_%'");
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wisor_elementor_events_%'");
        });
    }
    
    // Register the "Events" custom post type
    public function register_events_cpt() {
        $labels = array(
            'name'          => __('Events', 'wisor-events-plugin'),
            'singular_name' => __('Event', 'wisor-events-plugin'),
            'menu_name'     => __('Events', 'wisor-events-plugin'),
            'add_new'       => __('Add New Event', 'wisor-events-plugin'),
            'add_new_item'  => __('Add New Event', 'wisor-events-plugin'),
            'edit_item'     => __('Edit Event', 'wisor-events-plugin'),
            'new_item'      => __('New Event', 'wisor-events-plugin'),
            'view_item'     => __('View Event', 'wisor-events-plugin'),
            'all_items'     => __('All Events', 'wisor-events-plugin'),
        );

        $args = array(
            'label'         => __('Events', 'wisor-events-plugin'),
            'labels'        => $labels,
            'public'        => true,
            'menu_icon'     => 'dashicons-calendar',
            'supports'      => array('title', 'editor', 'thumbnail'),
            'has_archive'   => true,
            'rewrite'       => array('slug' => 'events'),
            'show_in_rest'  => true, // Enables Gutenberg support
        );

        register_post_type('events', $args);
    }

    // Add meta box for event date
    public function add_event_date_meta_box() {
        add_meta_box(
            'event_date_meta_box',
            __('Event Date', 'wisor-events-plugin'),
            array($this, 'display_event_date_meta_box'),
            'events',
            'side',
            'default'
        );
    }

    // Display meta box HTML
    public function display_event_date_meta_box($post) {
        $event_date = get_post_meta($post->ID, '_event_date', true);
        wp_nonce_field('save_event_date', 'event_date_nonce');
        ?>
        <label for="event_date"><?php _e('Select Event Date:', 'wisor-events-plugin'); ?></label>
        <input type="date" id="event_date" name="event_date" value="<?php echo esc_attr($event_date); ?>" style="width:100%;">
        <?php
    }

    // Save event date meta field
    public function save_event_date($post_id) {
        // Check if nonce is set and valid
        if (!isset($_POST['event_date_nonce']) || !wp_verify_nonce($_POST['event_date_nonce'], 'save_event_date')) {
            return;
        }
    
        // Prevent auto-saving from overwriting the data
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
    
        // Ensure user has permission to edit posts
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    
        // Validate and sanitize the event date
        if (isset($_POST['event_date'])) {
            $event_date = sanitize_text_field($_POST['event_date']); // Remove harmful input
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $event_date)) {  // Validate format (YYYY-MM-DD)
                update_post_meta($post_id, '_event_date', $event_date);
            }
        }
    }
    
}

// Initialize the class
new Wisor_Events_CPT();

// Automatically insert event date at the beginning of the content
add_filter('the_content', function ($content) {
    if (is_singular('events')) {
        $event_date = get_post_meta(get_the_ID(), '_event_date', true);
        if (!empty($event_date)) {
            $date_html = '<p><strong>Event Date:</strong> ' . esc_html($event_date) . '</p>';
            return $date_html . $content; // Insert event date before content
        }
    }
    return $content;
});

?>
