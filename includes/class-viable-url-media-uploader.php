<?php
/**
 * Main Plugin Class
 * 
 * @package Viable_URL_Media_Uploader
 * @author Ahsan Gadit
 */

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
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_init', array($this, 'init_upload_handler'));
        add_action('admin_menu', array($this, 'add_upload_page'));
        add_action('print_media_templates', array($this, 'render_media_template'));
        add_action('wp_ajax_vumu_upload_from_url', array($this, 'ajax_upload_handler'));
        
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
     * Load plugin textdomain for translations
     *
     * @author Ahsan Gadit
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'viable-url-media-uploader',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages'
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page hook
     * @author Ahsan Gadit
     */
    public function enqueue_assets($hook) {
        $allowed_hooks = array('post.php', 'post-new.php', 'upload.php');
        
        if (!in_array($hook, $allowed_hooks, true)) {
            return;
        }
        
        wp_enqueue_style(
            'vumu-admin-style',
            VUMU_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            VUMU_VERSION
        );
        
        wp_enqueue_script(
            'vumu-admin-script',
            VUMU_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery', 'media-upload'),
            VUMU_VERSION,
            true
        );
        
        wp_localize_script('vumu-admin-script', 'vumuData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vumu_upload_nonce'),
            'uploading' => __('Uploading...', 'viable-url-media-uploader'),
            'success' => __('File uploaded successfully!', 'viable-url-media-uploader'),
            'error' => __('An error occurred. Please try again.', 'viable-url-media-uploader'),
            'editUrlBase' => admin_url('post.php?post='),
        ));
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
     * Add upload page to media menu
     *
     * @author Ahsan Gadit
     */
    public function add_upload_page() {
        add_media_page(
            __('Upload from URL', 'viable-url-media-uploader'),
            __('Upload from URL', 'viable-url-media-uploader'),
            'upload_files',
            'vumu-upload-from-url',
            array($this, 'render_upload_page')
        );
    }
    
    /**
     * Render the upload page
     *
     * @author Ahsan Gadit
     */
    public function render_upload_page() {
        if (!current_user_can('upload_files')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'viable-url-media-uploader'));
        }
        
        include VUMU_PLUGIN_DIR . 'includes/upload-page.php';
    }
    
    /**
     * Render media modal template
     *
     * @author Ahsan Gadit
     */
    public function render_media_template() {
        include VUMU_PLUGIN_DIR . 'includes/media-template.php';
    }
    
    /**
     * AJAX handler for URL upload
     *
     * @author Ahsan Gadit
     */
    public function ajax_upload_handler() {
        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-upload-handler.php';
        VUMU_Upload_Handler::ajax_handle_upload();
    }
}

