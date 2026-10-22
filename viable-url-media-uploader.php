<?php
/**
 * Plugin Name: Viable URL Media Uploader
 * Plugin URI: https://vumu.viablecube.com/
 * Author: Ahsan Gadit
 * Author URI: https://github.com/ahsangadit
 * Text Domain: viable-url-media-uploader
 * Domain Path: /languages
 * Version: 1.1.0
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

if (!defined('FS_SDK__SSLVERIFY')) {
    define('FS_SDK__SSLVERIFY', true);
}

define('VUMU_VERSION', '1.1.0');
define('VUMU_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VUMU_PLUGIN_URL', plugin_dir_url(__FILE__));

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

            // Freemius SDK lives at freemius/ in this plugin (not under vendor/).
            $freemius_start = VUMU_PLUGIN_DIR . 'freemius/start.php';

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
                    'has_addons'          => true,
                    'has_paid_plans'      => false,
                    'has_premium_version' => true,
                    'is_org_compliant'    => true,
                    'menu'             => array(
                        'slug'       => 'vumu-upload-from-url',
                        'first-path' => 'upload.php?page=vumu-upload-from-url',
                        'account'    => false,
                        'contact'    => false,
                        'support'    => false,
                        'addons'     => false,
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
}

require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-helpers.php';
require_once VUMU_PLUGIN_DIR . 'includes/class-viable-url-media-uploader.php';

\ahsangadit\viable_url_media_uploader\Viable_URL_Media_Uploader::boot();

if ( function_exists( 'vumu_fs' ) ) {
    $vumu_fs = vumu_fs();

    if ( $vumu_fs ) {
        $vumu_fs->add_filter(
            'is_submenu_visible',
            static function ( $is_visible, $id ) {
                if ( in_array( $id, array( 'addons', 'support' ), true ) ) {
                    return false;
                }

                return $is_visible;
            },
            10,
            2
        );
    }

    /**
     * Pro licensing is handled by the add-on plugin only (free product has no paid plans).
     */
    $vumu_remove_free_license_action_link = static function ( $links ) {
        if ( ! is_array( $links ) ) {
            return $links;
        }

        foreach ( $links as $key => $link ) {
            if ( 'deactivate' === $key || ! is_string( $link ) ) {
                continue;
            }

            $is_license_link = (
                false !== stripos( $link, 'activate license' )
                || false !== stripos( $link, 'change license' )
                || false !== strpos( $link, 'activate-license' )
            );

            if ( $is_license_link ) {
                unset( $links[ $key ] );
            }
        }

        return $links;
    };

    add_filter( 'plugin_action_links_viable-url-media-uploader/viable-url-media-uploader.php', $vumu_remove_free_license_action_link, 999 );
    add_filter( 'network_admin_plugin_action_links_viable-url-media-uploader/viable-url-media-uploader.php', $vumu_remove_free_license_action_link, 999 );

    if ( $vumu_fs ) {
        add_action(
            'admin_init',
            static function () use ( $vumu_fs ) {
                if ( $vumu_fs->is_premium() ) {
                    return;
                }

                remove_action( 'admin_footer', array( $vumu_fs, '_add_license_activation_dialog_box' ) );
            },
            20
        );

        do_action( 'vumu_fs_loaded' );
    }
}
