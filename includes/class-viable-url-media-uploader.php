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

        add_filter(
            'plugin_action_links_' . plugin_basename(VUMU_PLUGIN_DIR . 'viable-url-media-uploader.php'),
            array($this, 'filter_plugins_list_action_links'),
            15
        );
        add_filter(
            'network_admin_plugin_action_links_' . plugin_basename(VUMU_PLUGIN_DIR . 'viable-url-media-uploader.php'),
            array($this, 'filter_plugins_list_action_links'),
            15
        );
        add_action('admin_head', array($this, 'add_plugins_page_pro_link_styles'));

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
            'vumu-admin-fonts',
            VUMU_PLUGIN_URL . 'assets/fonts/fonts.css',
            array(),
            VUMU_VERSION
        );

        wp_enqueue_style(
            'vumu-admin-style',
            VUMU_PLUGIN_URL . 'assets/css/admin-style.css',
            array( 'vumu-admin-fonts' ),
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
                        add_query_arg(
                            array(
                                'utm_source'   => 'vumu',
                                'utm_medium'   => 'plugin-upsell',
                                'utm_campaign' => 'upgrade',
                            ),
                            vumu_marketing_site_url()
                        )
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
        require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-upload-handler.php';
        VUMU_Upload_Handler::handle_upload();
    }

    /**
     * Plugins list row: Get VUMU Pro (green) | Settings | Visit us | …
     *
     * @param string[] $links Existing action links.
     * @return string[]
     */
    public function filter_plugins_list_action_links($links) {
        if (!current_user_can('manage_options')) {
            return $links;
        }

        $custom_links = array(
            sprintf(
                '<a href="%s">%s</a>',
                esc_url(admin_url('upload.php?page=vumu-upload-from-url')),
                esc_html__('Settings', 'viable-url-media-uploader')
            ),
            sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
                esc_url(vumu_marketing_site_url()),
                esc_html__('Visit us', 'viable-url-media-uploader')
            ),
        );

        if (!\vumu_has_pro_plugin()) {
            $upgrade_url = apply_filters(
                'vumu_pro_upgrade_url',
                add_query_arg(
                    array(
                        'utm_source'   => 'vumu',
                        'utm_medium'   => 'plugins_page',
                        'utm_campaign' => 'get-pro',
                    ),
                    vumu_marketing_site_url()
                )
            );

            array_unshift(
                $custom_links,
                sprintf(
                    '<a href="%s" target="_blank" rel="noopener noreferrer" class="vumu-pro-link">%s</a>',
                    esc_url($upgrade_url),
                    esc_html__('Get VUMU Pro', 'viable-url-media-uploader')
                )
            );
        }

        return array_merge($custom_links, $links);
    }

    /**
     * Green bold style for the Get VUMU Pro link on Plugins (matches Post SMTP).
     */
    public function add_plugins_page_pro_link_styles() {
        global $pagenow;

        if ('plugins.php' !== $pagenow || \vumu_has_pro_plugin()) {
            return;
        }
        ?>
        <style type="text/css">
            .vumu-pro-link {
                color: #00a32a !important;
                font-weight: bold;
            }
        </style>
        <?php
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

        $vars           = array('id' => $fs->get_id());
        $connect_markup = $fs->apply_filters('templates/connect.php', fs_get_template('connect.php', $vars));

        // Freemius SDK connect screen markup (same output as Freemius::_connect_page_render()).
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $connect_markup;

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
            if (
                class_exists('\ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin')
                && \ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin::should_show_license_ui()
            ) {
                return;
            }

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
