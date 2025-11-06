<?php
/**
 * Plugin Name: Viable URL Media Uploader
 * Plugin URI: https://github.com/yourusername/viable-url-media-uploader
 * Author: Ahsan Gadit
 * Author URI: https://yourwebsite.com
 * Text Domain: viable-url-media-uploader
 * Domain Path: /languages
 * Version: 1.0.0
 * Description: Add media files from URL directly to your WordPress media library with a simple input field and button in the media modal.
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * 
 * @author Ahsan Gadit
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VUMU_VERSION', '1.0.0');
define('VUMU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VUMU_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include main plugin class
require_once VUMU_PLUGIN_DIR . 'includes/class-viable-url-media-uploader.php';

/**
 * Initialize plugin
 *
 * @author Ahsan Gadit
 */
function vumu_init() {
    return Viable_URL_Media_Uploader::get_instance();
}

vumu_init();

/**
 * Enqueue admin CSS only for plugin upload page
 */
add_action('admin_enqueue_scripts', 'vumu_enqueue_admin_styles');
function vumu_enqueue_admin_styles($hook) {
    // The hook name for add_media_page() is "media_page_{slug}"
    if ($hook !== 'media_page_vumu-upload-from-url') {
        return;
    }

    wp_enqueue_style(
        'vumu-admin-style',
        VUMU_PLUGIN_URL . 'assets/css/admin-style.css',
        array(),
        VUMU_VERSION
    );
}

