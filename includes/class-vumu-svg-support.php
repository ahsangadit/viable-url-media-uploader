<?php
/**
 * SVG Support Class
 * Enables SVG file uploads in WordPress
 * 
 * @package ahsangadit\viable_url_media_uploader\Admin
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if (!defined('ABSPATH')) {
    exit;
}

class VUMU_SVG_Support {
    
    /**
     * Initialize SVG support
     *
     * @author Ahsan Gadit
     */
    public static function init() {
        // Allow SVG uploads
        add_filter('upload_mimes', array(__CLASS__, 'add_svg_mime_types'), 10, 1);
        
        // Fix SVG display in media library
        add_filter('wp_prepare_attachment_for_js', array(__CLASS__, 'fix_svg_media_library'), 10, 3);
        
        // Enqueue SVG admin styles using WordPress 5.7+ best practices
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_svg_admin_styles'));
    }
    
    /**
     * Add SVG to allowed mime types
     *
     * @param array $mimes Existing mime types
     * @return array Modified mime types
     * @author Ahsan Gadit
     */
    public static function add_svg_mime_types($mimes) {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['svgz'] = 'image/svg+xml';
        return $mimes;
    }
    
    /**
     * Fix SVG preview in media library
     *
     * @param array $response Attachment data
     * @param object $attachment Attachment object
     * @param array $meta Attachment metadata
     * @return array Modified response
     * @author Ahsan Gadit
     */
    public static function fix_svg_media_library($response, $attachment, $meta) {
        if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml') {
            $response['image'] = array(
                'src' => $response['url'],
                'width' => 150,
                'height' => 150
            );
            $response['thumb'] = array(
                'src' => $response['url'],
                'width' => 150,
                'height' => 150
            );
        }
        return $response;
    }
    
    /**
     * Enqueue admin styles for SVG preview using WordPress 5.7+ best practices
     *
     * @param string $hook Current admin page hook
     * @author Ahsan Gadit
     */
    public static function enqueue_svg_admin_styles($hook) {
        // Enqueue on media library pages and post edit pages (where media modal is used)
        $allowed_hooks = array(
            'upload.php',
            'post.php',
            'post-new.php',
            'media_page_vumu-upload-from-url'
        );
        
        // Also allow on any admin page where media library might be accessed
        $screen = get_current_screen();
        $should_enqueue = false;
        
        // Check if current hook is in allowed list
        if (in_array($hook, $allowed_hooks, true)) {
            $should_enqueue = true;
        }
        
        // Also enqueue on attachment edit pages
        if ($screen && 'attachment' === $screen->post_type) {
            $should_enqueue = true;
        }
        
        if (!$should_enqueue) {
            return;
        }
        
        // Register and enqueue a style handle for SVG support
        wp_register_style(
            'vumu-svg-admin-style',
            false,
            array(),
            VUMU_VERSION
        );
        
        wp_enqueue_style('vumu-svg-admin-style');
        
        // Add inline styles using wp_add_inline_style (WordPress 5.7+ best practice)
        $svg_css = '
            .attachment-266x266, .thumbnail img[src$=".svg"],
            img[src$=".svg"].attachment-post-thumbnail,
            .media-icon img[src$=".svg"] {
                width: 100% !important;
                height: auto !important;
            }
        ';
        
        wp_add_inline_style('vumu-svg-admin-style', $svg_css);
    }
}