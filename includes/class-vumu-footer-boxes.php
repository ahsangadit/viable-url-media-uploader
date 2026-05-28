<?php
/**
 * Footer Boxes Class
 * Renders support, feature request, and review boxes on the upload page.
 *
 * @package ahsangadit\viable_url_media_uploader
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUMU_Footer_Boxes {

	/**
	 * Render footer boxes section.
	 *
	 * @author Ahsan Gadit
	 */
	public static function render() {
		$boxes = apply_filters(
			'vumu_footer_boxes',
			array(
				array(
					'icon'        => '💬',
					'title'       => __( 'Need Help?', 'viable-url-media-uploader' ),
					'description' => __( 'Having trouble uploading files? Our support team is ready to help you resolve any issues quickly.', 'viable-url-media-uploader' ),
					'button_text' => __( 'Get Support', 'viable-url-media-uploader' ),
					'button_url'  => 'https://viablecube.com/support/?utm_source=vumu&utm_medium=plugin-footer&utm_campaign=support',
					'class'       => 'vumu-footer-support',
				),
				array(
					'icon'        => '💡',
					'title'       => __( 'Feature Request', 'viable-url-media-uploader' ),
					'description' => __( 'Have an idea to improve this plugin? We would love to hear your suggestions and feature requests.', 'viable-url-media-uploader' ),
					'button_text' => __( 'Request a Feature', 'viable-url-media-uploader' ),
					'button_url'  => 'https://viablecube.com/feature-request/?utm_source=vumu&utm_medium=plugin-footer&utm_campaign=feature-request',
					'class'       => 'vumu-footer-feature',
				),
				array(
					'icon'        => '⭐',
					'title'       => __( 'Enjoying the Plugin?', 'viable-url-media-uploader' ),
					'description' => __( 'If this plugin helps you, please leave a review on WordPress.org. Your feedback helps us grow and improve.', 'viable-url-media-uploader' ),
					'button_text' => __( 'Leave a Review', 'viable-url-media-uploader' ),
					'button_url'  => 'https://wordpress.org/support/plugin/viable-url-media-uploader/reviews/#new-post',
					'class'       => 'vumu-footer-review',
				),
			)
		);

		if ( empty( $boxes ) ) {
			return;
		}
		?>
		<div class="vumu-sidebar-section">
			<?php foreach ( $boxes as $box ) : ?>
				<div class="vumu-sidebar-box <?php echo esc_attr( $box['class'] ?? '' ); ?>">
					<div class="vumu-sidebar-icon"><?php echo esc_html( $box['icon'] ?? '' ); ?></div>
					<h3><?php echo esc_html( $box['title'] ?? '' ); ?></h3>
					<p><?php echo esc_html( $box['description'] ?? '' ); ?></p>
					<?php if ( ! empty( $box['button_url'] ) && ! empty( $box['button_text'] ) ) : ?>
						<a
							href="<?php echo esc_url( $box['button_url'] ); ?>"
							class="vumu-sidebar-btn"
							target="_blank"
							rel="noopener noreferrer"
						>
							<?php echo esc_html( $box['button_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
