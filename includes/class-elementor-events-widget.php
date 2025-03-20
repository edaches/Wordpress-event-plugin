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

        // Typography for Date
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'date_typography',
                'label'    => __('Date Typography', 'wisor-events-plugin'),
                'selector' => '{{WRAPPER}} .wisor-events-container .event-date',
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        // Get user settings from Elementor panel
        $settings = $this->get_settings_for_display();
        $default_limit = get_option('wisor_events_default_limit', 5);
        $events_count = !empty($settings['events_count']) ? intval($settings['events_count']) : intval($default_limit);

        // Get layout style (list or grid)
        $layout_class = isset($settings['layout_style']) && $settings['layout_style'] === 'grid' ? 'wisor-events-grid' : 'wisor-events-list';

        $query = new WP_Query([
            'post_type'      => 'events',
            'posts_per_page' => $events_count,
            'meta_key'       => '_event_date',
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => '_event_date',
                    'value'   => date('Y-m-d'),
                    'compare' => '>=',
                    'type'    => 'DATE'
                ]
            ]
        ]);

        echo '<div class="wisor-events-container ' . esc_attr($layout_class) . '">';

        if ($query->have_posts()) {
            echo '<ul class="wisor-events">';
            while ($query->have_posts()) {
                $query->the_post();
                $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                $event_link = get_permalink(get_the_ID());

                echo '<li>';

                // Clickable event image
                if (has_post_thumbnail()) {
                    echo '<a href="' . esc_url($event_link) . '">';
                    the_post_thumbnail('medium');
                    echo '</a><br>';
                }

                // Clickable event title
                echo '<h3><a href="' . esc_url($event_link) . '">' . esc_html(get_the_title()) . '</a></h3>';
                
                // Event date with a class for styling
                echo '<p class="event-date"><strong>Date:</strong> ' . esc_html($event_date) . '</p>';
                echo '<p>' . esc_html(get_the_excerpt()) . '</p>';

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
        data-layout="' . esc_attr($layout_class) . '">Load More</button>';


        wp_reset_postdata();
    }
}

?>
