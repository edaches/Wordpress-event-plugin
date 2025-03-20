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
            array(
                'limit'       => get_option('wisor_events_default_limit', 5),
                'filter'      => 'future',  // Options: future, past, all
                'order'       => 'ASC',     // Sort order: ASC (oldest first), DESC (newest first)
                'align_list'  => 'left',    // Align entire event list
                'align_text'  => 'left',    // Align event content (title, date, description)
                'desc_length' => 100        // Default description length
            ),
            $atts,
            'wisor_events'
        );

        // Generate a unique cache key based on shortcode attributes
        $cache_key = 'wisor_events_cache_' . md5(json_encode($atts));
    
        // Try to get cached results
        $events_html = get_transient($cache_key);
    
        // If cached data doesn't exist, run the query and cache the result
        if ($events_html === false) {
            $date_compare = ($atts['filter'] === 'past') ? '<' : '>=';
            $meta_query = [];

            if ($atts['filter'] !== 'all') {
                $meta_query[] = [
                    'key'     => '_event_date',
                    'value'   => date('Y-m-d'),
                    'compare' => $date_compare,
                    'type'    => 'DATE'
                ];
            }

            $query = new WP_Query([
                'post_type'      => 'events',
                'posts_per_page' => intval($atts['limit']),
                'meta_key'       => '_event_date',
                'orderby'        => 'meta_value',
                'order'          => sanitize_text_field($atts['order']),
                'meta_query'     => $meta_query
            ]);

            // Start capturing output buffer (prevents direct echoing)
            ob_start();

            // Apply alignment styles dynamically
            echo '<div class="wisor-events-container" style="text-align: ' . esc_attr($atts['align_list']) . ';">';
            
            if ($query->have_posts()) {
                echo '<ul class="wisor-events">';
                while ($query->have_posts()) {
                    $query->the_post();
                    $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                    $description = get_the_excerpt();
                    $trimmed_desc = wp_trim_words($description, intval($atts['desc_length']), '...');

                    // Display each event
                    echo '<li class="event-list-item" style="text-align: ' . esc_attr($atts['align_text']) . ';">';

                    // Event Image
                    if (has_post_thumbnail()) {
                        echo '<div class="event-image"><a href="' . esc_url(get_permalink()) . '">';
                        the_post_thumbnail('medium');
                        echo '</a></div>';
                    }

                    // Event Title & Date
                    echo '<h3><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></h3>';
                    echo '<p class="event-date"><strong>Date:</strong> ' . esc_html($event_date) . '</p>';
                    
                    // Event Description
                    echo '<p>' . esc_html($trimmed_desc) . '</p>';
                    echo '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p>' . esc_html__('No events found.', 'wisor-events-plugin') . '</p>';
            }

            echo '</div>'; // Close .wisor-events-container
            wp_reset_postdata();

            // Get output buffer contents and store in cache
            $events_html = ob_get_clean();
            set_transient($cache_key, $events_html, HOUR_IN_SECONDS);
        }
    
        return $events_html;
    }
}

// Initialize the class
new Wisor_Events_Shortcode();
?>
