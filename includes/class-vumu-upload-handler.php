<?php
/**
 * Upload Handler Class
 * Handles all upload operations
 * 
 * @package ahsangadit\viable_url_media_uploader\Admin
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if (!defined('ABSPATH')) {
    exit;
}

class VUMU_Upload_Handler {
    
    /**
     * Handle form-based upload (non-AJAX)
     *
     * @author Ahsan Gadit
     */
    public static function handle_upload() {
        global $pagenow;
        
        if ('upload.php' !== $pagenow) {
            return;
        }
        
        if (!current_user_can('upload_files')) {
            return;
        }
        
        if (!isset($_POST['vumu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['vumu_nonce'])), 'vumu_upload_nonce')) {
            return;
        }
        
        // Batch uploads are handled by the Pro plugin.
        if ( \vumu_is_pro_active() && isset( $_POST['vumu_urls'] ) ) {
            return;
        }

        if (!isset($_POST['vumu_url']) || empty($_POST['vumu_url'])) {
            return;
        }
        
        $url = esc_url_raw(wp_unslash($_POST['vumu_url']));
        
        if (!wp_http_validate_url($url)) {
            set_transient('vumu_upload_error', __('Invalid URL provided.', 'viable-url-media-uploader'), 30);
            return;
        }
        
        if (!class_exists(__NAMESPACE__ . '\\VUMU_URL_Handler')) {
            require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-url-handler.php';
        }
        
        $attachment_id = VUMU_URL_Handler::download_and_save($url);
        
        if (is_wp_error($attachment_id)) {
            set_transient('vumu_upload_error', $attachment_id->get_error_message(), 30);
            return;
        }
        
        wp_safe_redirect(admin_url('post.php?post=' . $attachment_id . '&action=edit'));
        exit;
    }
    
    /**
     * Handle AJAX upload
     *
     * @author Ahsan Gadit
     */
    public static function ajax_handle_upload() {
        if (!current_user_can('upload_files')) {
            wp_send_json_error(array(
                'message' => __('You do not have permission to upload files.', 'viable-url-media-uploader')
            ));
        }
        
        if (!isset($_POST['vumu_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['vumu_nonce'])), 'vumu_upload_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security check failed.', 'viable-url-media-uploader')
            ));
        }
        
        if (!isset($_POST['vumu_url']) || empty($_POST['vumu_url'])) {
            wp_send_json_error(array(
                'message' => __('Please provide a URL.', 'viable-url-media-uploader')
            ));
        }
        
        $url = esc_url_raw(wp_unslash($_POST['vumu_url']));
        
        if (!wp_http_validate_url($url)) {
            wp_send_json_error(array(
                'message' => __('Invalid URL provided.', 'viable-url-media-uploader')
            ));
        }
        
        if (!class_exists(__NAMESPACE__ . '\\VUMU_URL_Handler')) {
            require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-url-handler.php';
        }
        
        $attachment_id = VUMU_URL_Handler::download_and_save($url);
        
        if (is_wp_error($attachment_id)) {
            wp_send_json_error(array(
                'message' => $attachment_id->get_error_message()
            ));
        }
        
        wp_send_json_success(array(
            'attachment_id' => $attachment_id,
            'url' => wp_get_attachment_url($attachment_id),
            'edit_url' => admin_url('post.php?post=' . $attachment_id . '&action=edit')
        ));
    }
}