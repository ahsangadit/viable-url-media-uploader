<?php
/**
 * Plugin helper functions (global scope for templates and hooks).
 *
 * @package ahsangadit\viable_url_media_uploader
 * @author Ahsan Gadit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'vumu_is_pro_active' ) ) {
	/**
	 * Check whether the Pro plugin is active.
	 *
	 * @return bool
	 */
	function vumu_is_pro_active() {
		return (bool) apply_filters( 'vumu_is_pro_active', false );
	}
}

if ( ! function_exists( 'vumu_marketing_site_url' ) ) {
	/**
	 * Product marketing site base URL (no trailing slash).
	 *
	 * @param string $path Optional path relative to the product page (e.g. support.html).
	 * @return string
	 */
	function vumu_marketing_site_url( $path = '' ) {
		$base = untrailingslashit(
			apply_filters( 'vumu_marketing_site_url', 'https://vumu.viablecube.com' )
		);

		$path = ltrim( (string) $path, '/' );

		return $path ? $base . '/' . $path : $base . '/';
	}
}

if ( ! function_exists( 'vumu_has_pro_plugin' ) ) {
	/**
	 * Whether the Pro plugin is installed and active (license not required).
	 *
	 * @return bool
	 */
	function vumu_has_pro_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		return is_plugin_active( 'viable-url-media-uploader-pro/viable-url-media-uploader-pro.php' );
	}
}
