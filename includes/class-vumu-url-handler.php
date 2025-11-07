<?php
/**
 * URL Handler Class
 * Handles downloading files from URLs and saving to WordPress media library
 * 
 * @package ahsangadit\viable_url_media_uploader\Admin
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if (!defined('ABSPATH')) {
    exit;
}

class VUMU_URL_Handler {
    
    /**
     * Download and save file from URL to WordPress media library
     *
     * @param string $url The URL of the file to download
     * @param int $post_id Optional. The post ID to attach the file to
     * @return int|\WP_Error The attachment ID on success, or WP_Error on failure
     * @author Ahsan Gadit
     */
    public static function download_and_save($url, $post_id = 0) {
        // Include WordPress image functions
        if (!function_exists('wp_crop_image')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        
        // Include file functions
        if (!function_exists('get_file_description')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        
        // Include media functions
        if (!function_exists('media_upload_tabs')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }
        
        // Check if URL is valid
        if (!wp_http_validate_url($url)) {
            return new \WP_Error('invalid_url', __('Invalid URL provided.', 'viable-url-media-uploader'));
        }
        
        // Clean up URL - remove query strings from CDN URLs
        $url = self::clean_url($url);
        
        // Download the file
        $tmp_file = self::download_file($url);
        
        if (is_wp_error($tmp_file)) {
            return $tmp_file;
        }
        
        // Get file info
        $file_array = self::prepare_file_array($tmp_file, $url);
        
        if (is_wp_error($file_array)) {
            wp_delete_file($tmp_file);
            return $file_array;
        }
        
        // Upload to media library
        $attachment_id = media_handle_sideload($file_array, $post_id);
        
        // Clean up if error
        if (is_wp_error($attachment_id)) {
            wp_delete_file($file_array['tmp_name']);
            return $attachment_id;
        }
        
        // Generate attachment metadata and thumbnails (skip for SVG)
        $file_path = get_attached_file($attachment_id);
        $file_type = wp_check_filetype($file_path);
        
        // Don't generate thumbnails for SVG files
        if (isset($file_type['ext']) && strtolower($file_type['ext']) !== 'svg') {
            $attach_data = wp_generate_attachment_metadata($attachment_id, $file_path);
            wp_update_attachment_metadata($attachment_id, $attach_data);
        } else {
            // For SVG, just set basic metadata
            update_post_meta($attachment_id, '_wp_attachment_image_alt', '');
        }
        
        // Save original URL as post meta
        add_post_meta($attachment_id, '_vumu_original_url', $url, true);
        
        return $attachment_id;
    }
    
    /**
     * Clean URL from CDN parameters
     *
     * @param string $url URL to clean
     * @return string Cleaned URL
     * @author Ahsan Gadit
     */
    private static function clean_url($url) {
        $subdomains = array('i0.wp.com', 'i1.wp.com', 'i2.wp.com');
        $parsed = wp_parse_url($url);
        
        if (isset($parsed['host']) && in_array($parsed['host'], $subdomains)) {
            $url = remove_query_arg(array(
                'w', 'h', 'ssl', 'crop', 'resize', 'fit', 'lb', 'ulb',
                'filter', 'brightness', 'contrast', 'colorize', 'smooth',
                'zoom', 'quality', 'strip'
            ), $url);
        }
        
        return $url;
    }
    
    /**
     * Download file from URL to temporary location
     *
     * @param string $url URL to download from
     * @return string|\WP_Error Temporary file path or error
     * @author Ahsan Gadit
     */
    private static function download_file($url) {
        // Get filename from URL
        $url_filename = basename(wp_parse_url($url, PHP_URL_PATH));
        
        // Create temporary file
        $tmpfname = wp_tempnam($url_filename);
        
        if (!$tmpfname) {
            return new \WP_Error('http_no_file', __('Could not create temporary file.', 'viable-url-media-uploader'));
        }
        
        // Download with custom user agent and headers to avoid 403 errors
        $args = apply_filters('vumu_remote_get_args', array(
            'timeout' => 300, // 5 minutes - increased for large files and slow connections
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
            'headers' => array(
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Accept-Encoding' => 'gzip, deflate, br',
                'DNT' => '1',
                'Connection' => 'keep-alive',
                'Upgrade-Insecure-Requests' => '1',
            ),
            'stream' => true,
            'filename' => $tmpfname,
            'redirection' => 5,
            'blocking' => true,
            'sslverify' => false // Some servers have SSL issues
        ));
        
        $response = wp_safe_remote_get($url, $args);
        
        // If we get a 403, try with a different user-agent
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 403) {
            wp_delete_file($tmpfname);
            
            // Try with a more generic user-agent
            $args['user-agent'] = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
            $response = wp_safe_remote_get($url, $args);
            
            // If still 403, try with curl-like user-agent
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 403) {
                wp_delete_file($tmpfname);
                $args['user-agent'] = 'curl/7.68.0';
                $response = wp_safe_remote_get($url, $args);
            }
        }
        
        if (is_wp_error($response)) {
            wp_delete_file($tmpfname);
            return $response;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        
        if (200 !== $response_code) {
            wp_delete_file($tmpfname);
            
            $error_message = '';
            switch ($response_code) {
                case 403:
                    $error_message = __('Access denied. The server is blocking access to this file. Try using a different URL or contact the site owner.', 'viable-url-media-uploader');
                    break;
                case 404:
                    $error_message = __('File not found. Please check the URL and try again.', 'viable-url-media-uploader');
                    break;
                case 500:
                    $error_message = __('Server error. The remote server encountered an error. Please try again later.', 'viable-url-media-uploader');
                    break;
                default:
                    // translators: %s: HTTP response code (e.g., 403, 404, 500)
                    $error_message = sprintf(__('HTTP error: %s. Unable to download the file.', 'viable-url-media-uploader'), $response_code);
            }
            
            return new \WP_Error(
                'http_error',
                $error_message
            );
        }
        
        return $tmpfname;
    }
    
    /**
     * Prepare file array for WordPress upload
     *
     * @param string $tmp_file Temporary file path
     * @param string $url Original URL
     * @return array|\WP_Error File array for media_handle_sideload or error
     * @author Ahsan Gadit
     */
    private static function prepare_file_array($tmp_file, $url) {
        // Get file extension
        $file_extension = self::get_file_extension($url, $tmp_file);
        
        if (!$file_extension) {
            return new \WP_Error('no_extension', __('Could not determine file type.', 'viable-url-media-uploader'));
        }
        
        $file_array = array();
        $path = pathinfo($tmp_file);
        $ext_lower = strtolower(ltrim($file_extension, '.'));
        
        // Set temporary name
        if (!isset($path['extension']) || strtolower($path['extension']) !== $ext_lower) {
            // Rename temp file to have correct extension
            $new_tmp_file = $tmp_file . $file_extension;
            // Use copy and delete instead of rename for WordPress compatibility
            if (@copy($tmp_file, $new_tmp_file)) {
                $file_array['tmp_name'] = $new_tmp_file;
                wp_delete_file($tmp_file);
            } else {
                // If copy fails, use original file
                $file_array['tmp_name'] = $tmp_file;
            }
        } else {
            $file_array['tmp_name'] = $tmp_file;
        }
        
        // Set file name
        $filename = pathinfo($url, PATHINFO_FILENAME);
        if (!$filename || empty($filename)) {
            $filename = 'uploaded_file';
        }
        $file_array['name'] = sanitize_file_name($filename . $file_extension);
        
        // Set type for better WordPress handling (optional but helpful)
        $mime_type = self::get_mime_type($file_extension);
        if ($mime_type && $mime_type !== 'application/octet-stream') {
            $file_array['type'] = $mime_type;
        }
        
        return $file_array;
    }
    
    /**
     * Get file extension from URL or file
     *
     * @param string $url Original URL
     * @param string $file_path Path to downloaded file
     * @return string File extension (e.g., '.jpg')
     * @author Ahsan Gadit
     */
    private static function get_file_extension($url, $file_path = '') {
        // First try to get extension from URL (most reliable)
        $parsed_url = wp_parse_url($url, PHP_URL_PATH);
        $url_ext = pathinfo($parsed_url, PATHINFO_EXTENSION);
        if ($url_ext) {
            $url_ext = strtolower($url_ext);
            // Handle SVG and SVGZ
            if ($url_ext === 'svg' || $url_ext === 'svgz') {
                return '.svg';
            }
            return '.' . $url_ext;
        }
        
        // Try to get extension from file content for SVG
        if ($file_path && file_exists($file_path)) {
            $content = file_get_contents($file_path, false, null, 0, 200);
            if ($content && preg_match('/<svg/i', $content)) {
                return '.svg';
            }
        }
        
        // Try to get extension from image type (doesn't work for SVG)
        if ($file_path && function_exists('exif_imagetype')) {
            $image_type = @exif_imagetype($file_path);
            if ($image_type) {
                $ext = image_type_to_extension($image_type);
                if ($ext) {
                    return $ext === '.jpeg' ? '.jpg' : $ext;
                }
            }
        }
        
        // Default to .jpg
        return '.jpg';
    }
    
    /**
     * Get mime type from extension
     *
     * @param string $extension File extension
     * @return string Mime type
     * @author Ahsan Gadit
     */
    private static function get_mime_type($extension) {
        $mime_types = array(
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'mp4' => 'video/mp4',
            'mp3' => 'audio/mpeg',
        );
        
        $extension = strtolower(ltrim($extension, '.'));
        
        return isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
    }
}