<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class Wisor_Events_Widget extends Widget_Base {
    public function get_name() {
        return 'wisor_events';
    }

    public function get_title() {
        return __('Wisor Events', 'wisor-events-plugin');
    }

    public function get_icon() {
        return 'eicon-calendar';
    }

    public function get_categories() {
        return ['general'];
    }

    protected function register_controls() {
        // Content Settings
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Settings', 'wisor-events-plugin'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );
    
        $this->add_control(
            'events_count',
            [
                'label'   => __('Number of Events', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 1,
                'max'     => 20,
                'default' => 5,
            ]
        );

        $this->add_control(
            'event_filter',
            [
                'label'   => __('Event Filter', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'future' => __('Upcoming Events', 'wisor-events-plugin'),
                    'past'   => __('Past Events', 'wisor-events-plugin'),
                    'all'    => __('All Events', 'wisor-events-plugin'),
                ],
                'default' => 'future',
            ]
        );
        

        $this->add_control(
            'layout_style',
            [
                'label'   => __('Layout Style', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'list'  => __('List View', 'wisor-events-plugin'),
                    'grid'  => __('Grid View', 'wisor-events-plugin'),
                ],
                'default'      => 'list',
                'render_type'  => 'template', // Forces real-time update in Elementor
                'selectors'    => [
                    '{{WRAPPER}} .wisor-events-container' => 'display: block;',
                ],
            ]
        );

        // Sort Order (Ascending or Descending)
        $this->add_control(
            'event_order',
            [
                'label'   => __('Sort Order', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'ASC'  => __('Ascending (Oldest First)', 'wisor-events-plugin'),
                    'DESC' => __('Descending (Newest First)', 'wisor-events-plugin'),
                ],
                'default' => 'ASC',
            ]
        );
    
        $this->end_controls_section();
    
        // Styling Options
        $this->start_controls_section(
            'style_section',
            [
                'label' => __('Styling', 'wisor-events-plugin'),
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        // Title Color
        $this->add_control(
            'title_color',
            [
                'label'     => __('Title Color', 'wisor-events-plugin'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wisor-events-container h3 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Event Date Color
        $this->add_control(
            'date_color',
            [
                'label'     => __('Date Color', 'wisor-events-plugin'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wisor-events-container .event-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Description Color
        $this->add_control(
            'desc_color',
            [
                'label'     => __('Description Color', 'wisor-events-plugin'),
                'type'      => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .wisor-events-container p' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Typography for Title
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'label'    => __('Title Typography', 'wisor-events-plugin'),
                'selector' => '{{WRAPPER}} .wisor-events-container h3 a',
            ]
        );

        // Typography for Description
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'description_typography',
                'label'    => __('Description Typography', 'wisor-events-plugin'),
                'selector' => '{{WRAPPER}} .wisor-events-container p',
            ]
        );

        $this->add_control(
            'event_description_length',
            [
                'label'   => __('Description Length', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'min'     => 5,
                'max'     => 300,
                'step'    => 10,
                'default' => 10,
                'description' => __('Set the maximum number of characters for the event description.', 'wisor-events-plugin'),
            ]
        );

        // Typography for Date
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'date_typography',
                'label'    => __('Date Typography', 'wisor-events-plugin'),
                'selector' => '{{WRAPPER}} .wisor-events-container .event-date',
            ]
        );

        // Event Content Alignment (Aligns content inside each event item)
        $this->add_control(
            'event_content_alignment',
            [
                'label'   => __('Event Content Alignment', 'wisor-events-plugin'),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left'   => [
                        'title' => __('Left', 'wisor-events-plugin'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'wisor-events-plugin'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'  => [
                        'title' => __('Right', 'wisor-events-plugin'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'selectors' => [
                    '{{WRAPPER}} .wisor-events li, 
                     {{WRAPPER}} .wisor-events .event-grid-item' => 'text-align: {{VALUE}};',
                     '{{WRAPPER}} .event-content' => 'text-align: {{VALUE}};',
                     
                    // Apply alignment to image wrapper so image moves with text
                    '{{WRAPPER}} .wisor-events .event-image' => 'text-align: {{VALUE}};',
                ],
            ]
        );


        $this->end_controls_section();
    }

    protected function render() {
        // Get user settings from Elementor panel
        $settings = $this->get_settings_for_display();
        $default_limit = get_option('wisor_events_default_limit', 5);
        $events_count = !empty($settings['events_count']) ? intval($settings['events_count']) : intval($default_limit);
        $event_filter = !empty($settings['event_filter']) ? $settings['event_filter'] : 'future';
        $event_order = !empty($settings['event_order']) ? $settings['event_order'] : 'ASC';

        // Define the date condition for filtering
        $meta_query = [];

        // Get layout style (list or grid)
        $layout_class = isset($settings['layout_style']) && $settings['layout_style'] === 'grid' ? 'wisor-events-grid' : 'wisor-events-list';

        if ($event_filter !== 'all') {
            $date_compare = ($event_filter === 'past') ? '<' : '>=';
            $meta_query[] = [
                'key'     => '_event_date',
                'value'   => date('Y-m-d'),
                'compare' => $date_compare,
                'type'    => 'DATE'
            ];
        }

        $query = new WP_Query([
            'post_type'      => 'events',
            'posts_per_page' => $events_count,
            'meta_key'       => '_event_date',
            'orderby'        => 'meta_value',
            'order'          => $event_order,
            'meta_query'     => $meta_query
        ]);

        echo '<div class="wisor-events-container ' . esc_attr($layout_class) . '">';

        if ($query->have_posts()) {
            echo '<ul class="wisor-events">';
            while ($query->have_posts()) {
                $query->the_post();
                $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                $event_link = get_permalink(get_the_ID());
                $description = get_the_excerpt();
                $char_limit = isset($settings['event_description_length']) ? intval($settings['event_description_length']) : 100;

                echo '<li>';
                echo '<div class="event-image">';
                // Clickable event image
                if (has_post_thumbnail()) {
                    echo '<a href="' . esc_url($event_link) . '">';
                    the_post_thumbnail('medium');
                    echo '</a><br>';
                }
                echo '</div>';
                echo '<div class="event-content">';
                // Clickable event title
                echo '<h3><a href="' . esc_url($event_link) . '">' . esc_html(get_the_title()) . '</a></h3>';
                
                // Event date with a class for styling
                echo '<p class="event-date"><strong>Date:</strong> ' . esc_html($event_date) . '</p>';
                echo '<p>' . esc_html(wp_trim_words($description, $char_limit, '...')) . '</p>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__('No upcoming events.', 'wisor-events-plugin') . '</p>';
        }

        echo '</div>'; // Close .wisor-events-container

        // Load More Button
        echo '<button id="load-more-events" class="load-more-btn"
        data-page="1" 
        data-events-per-page="' . esc_attr($events_count) . '" 
        data-layout="' . esc_attr($layout_class) . '" 
        data-desc-length="' . esc_attr($settings['event_description_length']) . '">Load More</button>';



        wp_reset_postdata();
    }
}

?>
