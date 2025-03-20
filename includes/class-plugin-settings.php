<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Wisor_Events_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    // Add settings page under "Settings" in the WP dashboard
    public function add_settings_page() {
        add_options_page(
            'Wisor Events Settings',
            'Wisor Events',
            'manage_options',
            'wisor-events-settings',
            array($this, 'settings_page_html')
        );
    }

    // Register the settings field
    public function register_settings() {
        register_setting('wisor_events_settings_group', 'wisor_events_default_limit');

        add_settings_section(
            'wisor_events_main_section',
            __('Event Display Settings', 'wisor-events-plugin'),
            null,
            'wisor-events-settings'
        );

        add_settings_field(
            'wisor_events_default_limit',
            __('Default Number of Events:', 'wisor-events-plugin'),
            array($this, 'default_limit_field_html'),
            'wisor-events-settings',
            'wisor_events_main_section'
        );
    }

    // HTML for the settings field
    public function default_limit_field_html() {
        $value = get_option('wisor_events_default_limit', 5);
        echo '<input type="number" name="wisor_events_default_limit" value="' . esc_attr($value) . '" min="1" max="20">';
    }

    // Render the settings page
    public function settings_page_html() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Wisor Events Settings', 'wisor-events-plugin'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wisor_events_settings_group');
                do_settings_sections('wisor-events-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the settings page
new Wisor_Events_Settings();
?>
