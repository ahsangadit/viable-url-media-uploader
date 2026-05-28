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
        add_action('admin_menu', array($this, 'add_upload_page'));
        
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
        
        if (!in_array($hook, $allowed_hooks, true)) {
            return;
        }
        
        wp_enqueue_style(
            'vumu-admin-style',
            VUMU_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            VUMU_VERSION
        );

        if ( ! \vumu_is_pro_active() ) {
            wp_enqueue_script(
                'vumu-pro-upsell',
                VUMU_PLUGIN_URL . 'assets/js/pro-upsell.js',
                array(),
                VUMU_VERSION,
                true
            );

            wp_localize_script(
                'vumu-pro-upsell',
                'vumuProUpsell',
                array(
                    'upgradeUrl' => apply_filters(
                        'vumu_pro_upgrade_url',
                        'https://viablecube.com/viable-url-media-uploader-pro/?utm_source=vumu&utm_medium=plugin-upsell&utm_campaign=upgrade'
                    ),
                    'tooltip'    => __( 'Multiple URL upload is available in Pro. Click to learn more.', 'viable-url-media-uploader' ),
                )
            );
        }
    }
    
    /**
     * Initialize upload handler
     *
     * @author Ahsan Gadit
     */
    public function init_upload_handler() {
        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-url-handler.php';
        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-upload-handler.php';
        VUMU_Upload_Handler::handle_upload();
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
     * Remove admin notices from other plugins on upload page
     * Runs in admin_init with high priority to remove notices before they're displayed
     *
     * @author Ahsan Gadit
     */
    public function remove_admin_notices() {
        global $pagenow;
        
        // Only remove notices on our upload page
        if ('upload.php' === $pagenow && isset($_GET['page']) && 'vumu-upload-from-url' === $_GET['page']) {
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
        
        include VUMU_PLUGIN_DIR . 'includes/upload-page.php';
    }
}