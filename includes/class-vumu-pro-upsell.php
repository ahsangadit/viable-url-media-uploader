<?php
/**
 * Pro Upsell Class
 * Highlights Pro features for free plugin users.
 *
 * @package ahsangadit\viable_url_media_uploader
 * @author Ahsan Gadit
 */

namespace ahsangadit\viable_url_media_uploader;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VUMU_Pro_Upsell {

	/**
	 * Render Pro upsell block below the free upload field.
	 */
	public static function render() {
		if ( \vumu_is_pro_active() ) {
			return;
		}

		$upgrade_url = apply_filters(
			'vumu_pro_upgrade_url',
			'https://viablecube.com/viable-url-media-uploader-pro/?utm_source=vumu&utm_medium=plugin-upsell&utm_campaign=upgrade'
		);
		?>
		<div class="vumu-pro-upsell">
			<div class="vumu-pro-upsell-preview" aria-hidden="true">
				<div class="vumu-pro-upsell-row">
					<span class="vumu-pro-upsell-row-label">
						<?php esc_html_e( 'File URL', 'viable-url-media-uploader' ); ?>
						<span class="vumu-pro-upsell-row-number">2</span>
					</span>
					<input
						type="url"
						class="vumu-pro-upsell-input"
						placeholder="<?php esc_attr_e( 'Enter another Image URL', 'viable-url-media-uploader' ); ?>"
						disabled
						tabindex="-1"
					/>
				</div>
			</div>

			<div class="vumu-pro-upsell-actions">
				<button
					type="button"
					class="vumu-pro-upsell-add-btn"
					aria-disabled="true"
					title="<?php esc_attr_e( 'Multiple URL upload is a Pro feature', 'viable-url-media-uploader' ); ?>"
				>
					<span class="vumu-pro-upsell-add-icon">
						<span class="vumu-pro-upsell-pulse-ring"></span>
						<span class="vumu-pro-upsell-plus">+</span>
					</span>
					<?php esc_html_e( 'Add URL', 'viable-url-media-uploader' ); ?>
					<span class="vumu-pro-upsell-badge"><?php esc_html_e( 'PRO', 'viable-url-media-uploader' ); ?></span>
				</button>
			</div>

			<p class="vumu-pro-upsell-message">
				<?php
				echo wp_kses_post(
					sprintf(
						/* translators: %s: upgrade link */
						__( 'Upload multiple files at once with <strong>Pro</strong>. %s', 'viable-url-media-uploader' ),
						'<a href="' . esc_url( $upgrade_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Upgrade to Pro →', 'viable-url-media-uploader' ) . '</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}
}
