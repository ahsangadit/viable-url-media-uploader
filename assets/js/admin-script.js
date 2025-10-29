/**
 * Admin Scripts for Viable URL Media Uploader
 */

(function($) {
    'use strict';

    var VUMU = {
        
        /**
         * Initialize
         */
        init: function() {
            this.handlePageForm();
            this.handleMediaModal();
        },
        
        /**
         * Handle form submission on upload page
         */
        handlePageForm: function() {
            $('#vumu-url-upload-form').on('submit', function(e) {
                e.preventDefault();
                
                var $form = $(this);
                var $urlInput = $('#vumu-url-input');
                var $btn = $('#vumu-upload-btn');
                var $message = $('#vumu-message');
                var url = $urlInput.val().trim();
                
                if (!url) {
                    VUMU.showMessage($message, 'error', 'Please enter a URL');
                    return false;
                }
                
                $btn.prop('disabled', true);
                $btn.find('.vumu-btn-text').text(vumuData.uploading || 'Uploading...');
                $message.html('<div class="notice notice-info"><p>' + (vumuData.uploading || 'Please wait, uploading...') + '</p></div>');
                
                // For non-AJAX form submission, let it proceed
                return true;
            });
        },
        
        /**
         * Handle media modal integration
         */
        handleMediaModal: function() {
            if (typeof wp === 'undefined' || !wp.media) {
                return;
            }
            
            var originalUploader = wp.media.view.Uploader;
            
            wp.media.view.Uploader = wp.media.view.Uploader.extend({
                initialize: function() {
                    originalUploader.prototype.initialize.apply(this, arguments);
                    this.on('ready', this.injectForm);
                },
                
                injectForm: function() {
                    if ($('#vumu-upload-container').length > 0) {
                        return;
                    }
                    
                    var template = $('#tmpl-vumu-upload-form').html();
                    $(this.uploader.$el).before(template);
                    
                    $('#vumu-url-upload-form').on('submit', function(e) {
                        e.preventDefault();
                        VUMU.handleAjaxUpload($(this));
                    });
                }
            });
        },
        
        /**
         * Handle AJAX upload
         */
        handleAjaxUpload: function($form) {
            var url = $('#vumu-url-input').val().trim();
            var $btn = $('#vumu-upload-btn');
            var $message = $('#vumu-message');
            
            if (!url) {
                VUMU.showMessage($message, 'error', 'Please enter a URL');
                return;
            }
            
            $btn.prop('disabled', true);
            $btn.find('.vumu-btn-text').text(vumuData.uploading || 'Uploading...');
            $message.html('');
            
            $.ajax({
                url: vumuData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'vumu_upload_from_url',
                    vumu_url: url,
                    vumu_nonce: vumuData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        VUMU.showMessage($message, 'success', vumuData.success || 'File uploaded successfully!');
                        $('#vumu-url-input').val('');
                        
                        if (typeof wp !== 'undefined' && wp.media && wp.media.frame) {
                            wp.media.frame.library._requery(true);
                        }
                        
                        setTimeout(function() {
                            if (response.data && response.data.edit_url) {
                                window.location.href = response.data.edit_url;
                            } else {
                                window.location.href = vumuData.editUrlBase.replace(/\/$/, '');
                            }
                        }, 1500);
                    } else {
                        var errorMsg = (response.data && response.data.message) || vumuData.error || 'An error occurred';
                        VUMU.showMessage($message, 'error', errorMsg);
                        $btn.prop('disabled', false);
                        $btn.find('.vumu-btn-text').text('Upload File');
                    }
                },
                error: function() {
                    VUMU.showMessage($message, 'error', vumuData.error || 'An error occurred');
                    $btn.prop('disabled', false);
                    $btn.find('.vumu-btn-text').text('Upload File');
                }
            });
        },
        
        /**
         * Show message to user
         */
        showMessage: function($container, type, message) {
            var className = type === 'error' ? 'notice-error' : 'notice-success';
            var html = '<div class="notice ' + className + '"><p>' + message + '</p></div>';
            $container.html(html);
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        VUMU.init();
    });
    
})(jQuery);
