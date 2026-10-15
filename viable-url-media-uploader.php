<?php
/**
 * Plugin Name: Viable URL Media Uploader
 * Plugin URI: https://viablecube.com/viable-url-media-uploader
 * Author: Ahsan Gadit
 * Author URI: https://github.com/ahsangadit
 * Text Domain: viable-url-media-uploader
 * Domain Path: /languages
 * Version: 1.0.2
 * Description: Add media files from URL directly to your WordPress media library with a simple input field and button in the media modal.
 * Requires at least: 5.6.0
 * Requires PHP: 7.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package ahsangadit\viable_url_media_uploader
 * @author Ahsan Gadit
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('vumu_fs')) {
    /**
     * Freemius SDK helper.
     *
     * @return \Freemius|null
     */
    function vumu_fs() {
        global $vumu_fs;

        if (!isset($vumu_fs)) {
            if (!defined('WP_FS__PRODUCT_41632_MULTISITE')) {
                define('WP_FS__PRODUCT_41632_MULTISITE', true);
            }

            $freemius_start = dirname(__FILE__) . '/freemius/start.php';
            if (!is_readable($freemius_start)) {
                $freemius_start = dirname(__FILE__) . '/vendor/freemius/start.php';
            }

            if (!is_readable($freemius_start)) {
                return null;
            }

            require_once $freemius_start;

            $vumu_fs = fs_dynamic_init(
                array(
                    'id'               => '41632',
                    'slug'             => 'viable-url-media-uploader',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_c1973284c19c8858490c8b253ed87',
                    'is_premium'       => false,
                    'has_addons'       => false,
                    'has_paid_plans'   => false,
                    'is_org_compliant' => true,
                    'menu'             => array(
                        'slug'       => 'vumu-upload-from-url',
                        'first-path' => 'upload.php?page=vumu-upload-from-url',
                        'account'    => false,
                        'contact'    => false,
                        'support'    => false,
                        'parent'     => array(
                            'slug' => 'upload.php',
                        ),
                    ),
                )
            );
        }

        return $vumu_fs;
    }

    vumu_fs();
    do_action('vumu_fs_loaded');
}

define('VUMU_VERSION', '1.0.2');
define('VUMU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VUMU_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once VUMU_PLUGIN_DIR . 'includes/class-viable-url-media-uploader.php';

\ahsangadit\viable_url_media_uploader\Viable_URL_Media_Uploader::boot();
