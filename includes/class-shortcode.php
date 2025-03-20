<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Events_Shortcode {
    public function __construct() {
        add_shortcode('wisor_events', array($this, 'render_events_shortcode'));
    }

    public function render_events_shortcode($atts) {
        // Set default attributes and merge with user input
        $atts = shortcode_atts(
            array('limit' => get_option('wisor_events_default_limit', 5)),  // Default to showing 5 events
            $atts,
            'wisor_events'
        );
    
        // Generate a unique cache key based on the number of events requested
        $cache_key = 'wisor_events_cache_' . intval($atts['limit']);
    
        // Try to get cached results
        $events_html = get_transient($cache_key);
    
        // If cached data doesn't exist, run the query and cache the result
        if ($events_html === false) {
            $query = new WP_Query([
                'post_type'      => 'events',         // Get only "Events" post type
                'posts_per_page' => intval($atts['limit']), // Limit the number of events
                'meta_key'       => '_event_date',    // Order by event date
                'orderby'        => 'meta_value',
                'order'          => 'ASC',            // Show earliest events first
                'meta_query'     => [
                    [
                        'key'     => '_event_date',   // Ensure only future events are shown
                        'value'   => date('Y-m-d'),  // Compare today's date
                        'compare' => '>=',
                        'type'    => 'DATE'
                    ]
                ]
            ]);
    
            // Start capturing output buffer (prevents direct echoing)
            ob_start();
    
            // Check if there are events
            if ($query->have_posts()) {
                echo '<ul class="wisor-events-list">';
                while ($query->have_posts()) {
                    $query->the_post();
                    $event_date = get_post_meta(get_the_ID(), '_event_date', true); // Get the event date
    
                    // Display each event in an unordered list and Escape output to prevent XSS
                    echo '<li>';
                    echo '<strong>' . esc_html(get_the_title()) . '</strong><br>';
                    echo '<em>Date: ' . esc_html($event_date) . '</em><br>';
                    echo '<p>' . esc_html(get_the_excerpt()) . '</p>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . esc_html__('No upcoming events.', 'wisor-events-plugin') . '</p>';
            }
    
            wp_reset_postdata(); // Restore global post data
    
            // Get output buffer contents and store in cache
            $events_html = ob_get_clean();
            set_transient($cache_key, $events_html, HOUR_IN_SECONDS); // Cache for 1 hour
        }
    
        // Return the cached events list
        return $events_html;
    }
    
}

// Initialize the class
new Wisor_Events_Shortcode();
?>
