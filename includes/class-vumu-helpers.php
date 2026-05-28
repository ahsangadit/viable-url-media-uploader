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
