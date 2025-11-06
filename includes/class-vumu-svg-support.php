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
        
        // Fix SVG preview in media library
        add_action('admin_head', array(__CLASS__, 'svg_admin_style'));
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
     * Add admin styles for SVG preview
     *
     * @author Ahsan Gadit
     */
    public static function svg_admin_style() {
        echo '<style>
            .attachment-266x266, .thumbnail img[src$=".svg"],
            img[src$=".svg"].attachment-post-thumbnail,
            .media-icon img[src$=".svg"] {
                width: 100% !important;
                height: auto !important;
            }
        </style>';
    }
}

