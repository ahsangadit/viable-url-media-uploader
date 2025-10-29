<?php
/**
 * Upload Page Template
 * 
 * @package Viable_URL_Media_Uploader
 */

if (!defined('ABSPATH')) {
    exit;
}

$error = get_transient('vumu_upload_error');
if ($error) {
    delete_transient('vumu_upload_error');
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <?php if (!empty($error)): ?>
        <div class="notice notice-error">
            <p><?php echo esc_html($error); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="vumu-upload-container">
        <form method="post" action="<?php echo esc_url(admin_url('upload.php?page=vumu-upload-from-url')); ?>" id="vumu-url-upload-form">
            <?php wp_nonce_field('vumu_upload_nonce', 'vumu_nonce'); ?>
            
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <label for="vumu-url-input"><?php esc_html_e('File URL', 'viable-url-media-uploader'); ?></label>
                        </th>
                        <td>
                            <input 
                                type="url" 
                                name="vumu_url" 
                                id="vumu-url-input" 
                                class="regular-text" 
                                placeholder="https://example.com/image.jpg"
                                required
                                autofocus
                            />
                            <p class="description">
                                <?php esc_html_e('Enter the full URL of the file you want to add to your media library.', 'viable-url-media-uploader'); ?>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p class="submit">
                <button type="submit" class="button button-primary button-large" id="vumu-upload-btn">
                    <span class="vumu-btn-text"><?php esc_html_e('Upload File', 'viable-url-media-uploader'); ?></span>
                </button>
            </p>
            
            <div id="vumu-message" style="margin-top: 15px;"></div>
        </form>
    </div>
    
    <div class="vumu-info" style="margin-top: 30px; padding: 15px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <h2 style="margin-top: 0;"><?php esc_html_e('How to use', 'viable-url-media-uploader'); ?></h2>
        <ol>
            <li><?php esc_html_e('Find the URL of the file you want to upload', 'viable-url-media-uploader'); ?></li>
            <li><?php esc_html_e('Paste the URL into the input field above', 'viable-url-media-uploader'); ?></li>
            <li><?php esc_html_e('Click the "Upload File" button', 'viable-url-media-uploader'); ?></li>
            <li><?php esc_html_e('The file will be downloaded and added to your media library', 'viable-url-media-uploader'); ?></li>
        </ol>
        
        <h3><?php esc_html_e('Supported file types', 'viable-url-media-uploader'); ?></h3>
        <p><?php esc_html_e('This plugin supports all file types that WordPress can handle:', 'viable-url-media-uploader'); ?></p>
        <ul>
            <li><strong><?php esc_html_e('Images:', 'viable-url-media-uploader'); ?></strong> JPG, PNG, GIF, WebP, SVG</li>
            <li><strong><?php esc_html_e('Videos:', 'viable-url-media-uploader'); ?></strong> MP4, WebM, MOV</li>
            <li><strong><?php esc_html_e('Documents:', 'viable-url-media-uploader'); ?></strong> PDF, DOC, DOCX, XLS, XLSX</li>
            <li><strong><?php esc_html_e('Audio:', 'viable-url-media-uploader'); ?></strong> MP3, WAV, OGG</li>
        </ul>
    </div>
</div>
