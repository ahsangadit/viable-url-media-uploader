<?php
/**
 * Main Plugin Class
 * 
 * @package ahsangadit\viable_url_media_uploader\Admin
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if (!defined('ABSPATH')) {
    exit;
}

class Viable_URL_Media_Uploader {

    private static $instance = null;

    /**
     * Load dependencies and start the plugin.
     *
     * @return Viable_URL_Media_Uploader
     */
    public static function boot() {
        self::load_svg_sanitizer_module();

        return self::get_instance();
    }

    /**
     * Load the SVG sanitizer and define availability for all upload paths.
     */
    private static function load_svg_sanitizer_module() {
        if (defined('VUMU_SVG_SANITIZER_AVAILABLE')) {
            return;
        }

        $available = false;
        $sanitizer_path = VUMU_PLUGIN_DIR . 'includes/class-vumu-svg-sanitizer.php';

        if (is_readable($sanitizer_path)) {
            require_once $sanitizer_path;
            $available = class_exists(__NAMESPACE__ . '\\VUMU_SVG_Sanitizer');
        }

        define('VUMU_SVG_SANITIZER_AVAILABLE', $available);

        if (!$available) {
            add_action('admin_notices', array(__CLASS__, 'render_svg_sanitizer_missing_notice'));
        }
    }

    /**
     * Admin notice when the SVG sanitizer file is missing from the plugin package.
     */
    public static function render_svg_sanitizer_missing_notice() {
        if (!current_user_can('activate_plugins')) {
            return;
        }

        echo '<div class="notice notice-error"><p>';
        echo esc_html__(
            'Viable URL Media Uploader is missing its SVG security module (class-vumu-svg-sanitizer.php). SVG uploads are disabled until you reinstall a complete copy of the plugin.',
            'viable-url-media-uploader'
        );
        echo '</p></div>';
    }

    /**
     * Get plugin instance
     *
     * @return Viable_URL_Media_Uploader
     * @author Ahsan Gadit
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     *
     * @author Ahsan Gadit
     */
    private function __construct() {
        $this->init_hooks();
    }
    
    /**
     * Initialize WordPress hooks
     *
     * @author Ahsan Gadit
     */
    private function init_hooks() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_init', array($this, 'init_upload_handler'));
        $this->register_admin_menu();

        add_action('load-media_page_vumu-upload-from-url', array($this, 'on_upload_admin_page_load'));

        // Hide admin notices from other plugins on upload page
        add_action('admin_init', array($this, 'remove_admin_notices'), 999);
        
        // Enable SVG support
        $this->init_svg_support();
    }
    
    /**
     * Initialize SVG support
     *
     * @author Ahsan Gadit
     */
    private function init_svg_support() {
        if (!defined('VUMU_SVG_SANITIZER_AVAILABLE') || !VUMU_SVG_SANITIZER_AVAILABLE) {
            return;
        }

        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-svg-support.php';
        VUMU_SVG_Support::init();
    }
    
    /**
     * Enqueue admin styles
     *
     * @param string $hook Current admin page hook
     * @author Ahsan Gadit
     */
    public function enqueue_styles($hook) {
        $allowed_hooks = array('media_page_vumu-upload-from-url');

        if (!in_array($hook, $allowed_hooks, true) || $this->should_show_freemius_connect_screen()) {
            return;
        }

        wp_enqueue_style(
            'vumu-admin-style',
            VUMU_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            VUMU_VERSION
        );
    }
    
    /**
     * Initialize upload handler
     *
     * @author Ahsan Gadit
     */
    public function init_upload_handler() {
        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-upload-handler.php';
        VUMU_Upload_Handler::handle_upload();
    }
    
    /**
     * Register the Media submenu so Freemius can override it for opt-in on the same URL.
     */
    private function register_admin_menu() {
        if (function_exists('vumu_fs') && vumu_fs()) {
            vumu_fs()->add_action('before_admin_menu_init', array($this, 'add_upload_page'));
            return;
        }

        add_action('admin_menu', array($this, 'add_upload_page'));
    }

    /**
     * Add upload page to media menu
     *
     * @author Ahsan Gadit
     */
    public function add_upload_page() {
        add_media_page(
            __('Viable URL Media', 'viable-url-media-uploader'),
            __('Viable URL Media', 'viable-url-media-uploader'),
            'upload_files',
            'vumu-upload-from-url',
            array($this, 'render_upload_page')
        );
    }

    /**
     * Prepare Freemius assets when the opt-in screen should appear on this admin page.
     */
    public function on_upload_admin_page_load() {
        if (!$this->should_show_freemius_connect_screen()) {
            return;
        }

        $fs = vumu_fs();
        if ($fs && is_callable(array($fs, '_enqueue_connect_essentials'))) {
            $fs->_enqueue_connect_essentials();
        }
    }

    /**
     * Whether the Freemius connect / opt-in UI should replace the upload screen.
     *
     * @return bool
     */
    private function should_show_freemius_connect_screen() {
        if (!function_exists('vumu_fs') || !function_exists('fs_get_template')) {
            return false;
        }

        $fs = vumu_fs();
        if (!$fs) {
            return false;
        }

        if (!$fs->is_activation_mode()) {
            return false;
        }

        if ($fs->is_registered() || $fs->is_anonymous() || $fs->is_pending_activation()) {
            return false;
        }

        return true;
    }

    /**
     * Output the Freemius connect template (same screen as SDK override).
     *
     * @return bool True when connect UI was rendered.
     */
    private function render_freemius_connect_screen() {
        if (!$this->should_show_freemius_connect_screen()) {
            return false;
        }

        $fs = vumu_fs();
        if (!$fs) {
            return false;
        }

        if (is_callable(array($fs, '_enqueue_connect_essentials'))) {
            $fs->_enqueue_connect_essentials();
        }

        $vars = array('id' => $fs->get_id());
        echo $fs->apply_filters('templates/connect.php', fs_get_template('connect.php', $vars));

        return true;
    }
    
    /**
     * Remove admin notices from other plugins on upload page
     * Runs in admin_init with high priority to remove notices before they're displayed
     *
     * @author Ahsan Gadit
     */
    public function remove_admin_notices() {
        global $pagenow;
        
        // Only remove notices on our upload page.
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin page slug check; no form processing.
        $current_page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if ('upload.php' === $pagenow && 'vumu-upload-from-url' === $current_page && !$this->should_show_freemius_connect_screen()) {
            // Remove all admin notices actions
            remove_all_actions('admin_notices');
            remove_all_actions('all_admin_notices');
        }
    }
    
    /**
     * Render the upload page
     *
     * @author Ahsan Gadit
     */
    public function render_upload_page() {
        if (!current_user_can('upload_files')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'viable-url-media-uploader'));
        }

        if ($this->render_freemius_connect_screen()) {
            return;
        }

        include VUMU_PLUGIN_DIR . 'includes/upload-page.php';
    }
}