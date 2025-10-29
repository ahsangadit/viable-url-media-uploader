<?php
/**
 * Media Modal Template
 * 
 * @package Viable_URL_Media_Uploader
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<script type="text/html" id="tmpl-vumu-upload-form">
    <div class="vumu-upload-container" style="padding: 15px;">
        <h3><?php esc_html_e('Upload from URL', 'viable-url-media-uploader'); ?></h3>
        <form id="vumu-url-upload-form" method="post">
            <div style="margin-bottom: 15px;">
                <label for="vumu-url-input" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <?php esc_html_e('File URL', 'viable-url-media-uploader'); ?>
                </label>
                <input 
                    type="url" 
                    id="vumu-url-input" 
                    name="vumu_url" 
                    class="large-text" 
                    placeholder="https://example.com/image.jpg"
                    required
                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                />
                <p class="description" style="margin-top: 5px; color: #666;">
                    <?php esc_html_e('Enter the full URL of the file you want to add to your media library.', 'viable-url-media-uploader'); ?>
                </p>
            </div>
            
            <button 
                type="submit" 
                id="vumu-upload-btn" 
                class="button button-primary button-large"
                style="width: 100%; padding: 10px; font-size: 14px;"
            >
                <span class="vumu-btn-text"><?php esc_html_e('Upload File', 'viable-url-media-uploader'); ?></span>
            </button>
            
            <div id="vumu-message" style="margin-top: 10px;"></div>
        </form>
    </div>
</script>
