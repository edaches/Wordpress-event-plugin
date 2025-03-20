<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Ensure Elementor is loaded before adding the widget
add_action('elementor/widgets/widgets_registered', function () {
    if (class_exists('Elementor\Widget_Base')) {
        require_once plugin_dir_path(__FILE__) . 'class-elementor-events-widget.php';
    }
});

// Register the custom Elementor widget
add_action('elementor/widgets/register', function ($widgets_manager) {
    require_once plugin_dir_path(__FILE__) . 'class-elementor-events-widget.php';
    $widgets_manager->register(new Wisor_Events_Widget());
});
?>
