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
	 * Admin URL for the URL imports tab (free preview or Pro log).
	 *
	 * @return string
	 */
	public static function get_imports_tab_url() {
		return add_query_arg(
			array(
				'page'     => 'vumu-upload-from-url',
				'vumu_tab' => 'imports',
			),
			admin_url( 'upload.php' )
		);
	}

	/**
	 * Render Pro upsell block below the free upload field.
	 */
	public static function render() {
		if ( \vumu_is_pro_active() ) {
			return;
		}

		$upgrade_url = apply_filters(
			'vumu_pro_upgrade_url',
			add_query_arg(
				array(
					'utm_source'   => 'vumu',
					'utm_medium'   => 'plugin-upsell',
					'utm_campaign' => 'upgrade',
				),
				vumu_marketing_site_url()
			)
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
						__( 'One image? Go free. A whole batch? %s', 'viable-url-media-uploader' ),
						'<a href="' . esc_url( $upgrade_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Get VUMU Pro →', 'viable-url-media-uploader' ) . '</a>'
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Render URL imports tab preview for free users (Pro feature teaser).
	 */
	public static function render_import_log() {
		if ( \vumu_is_pro_active() ) {
			return;
		}

		$upgrade_url = apply_filters(
			'vumu_pro_upgrade_url',
			add_query_arg(
				array(
					'utm_source'   => 'vumu',
					'utm_medium'   => 'plugin-upsell',
					'utm_campaign' => 'import-log',
				),
				vumu_marketing_site_url()
			)
		);

		$sample_rows = array(
			array(
				'file'   => __( 'hero-banner.webp', 'viable-url-media-uploader' ),
				'source' => 'https://cdn.example.com/images/hero-banner.webp',
				'date'   => __( 'Mar 12, 2026', 'viable-url-media-uploader' ),
			),
			array(
				'file'   => __( 'product-shot-01.jpg', 'viable-url-media-uploader' ),
				'source' => 'https://assets.example.com/shop/product-shot-01.jpg',
				'date'   => __( 'Mar 10, 2026', 'viable-url-media-uploader' ),
			),
			array(
				'file'   => __( 'team-photo.png', 'viable-url-media-uploader' ),
				'source' => 'https://media.example.org/uploads/team-photo.png',
				'date'   => __( 'Mar 8, 2026', 'viable-url-media-uploader' ),
			),
		);
		?>
		<div class="vumu-pro-import-upsell">
			<p class="vumu-pro-import-upsell__lead">
				<?php esc_html_e( 'Keep a searchable list of every file you imported — with preview, filename, source link, and date.', 'viable-url-media-uploader' ); ?>
			</p>

			<div class="vumu-pro-import-upsell__preview" aria-hidden="true">
				<div class="vumu-import-log__table-wrap vumu-surface-card">
					<table class="widefat striped vumu-import-log__table">
						<thead>
							<tr>
								<th scope="col" class="column-preview"><?php esc_html_e( 'Preview', 'viable-url-media-uploader' ); ?></th>
								<th scope="col" class="column-file"><?php esc_html_e( 'File', 'viable-url-media-uploader' ); ?></th>
								<th scope="col" class="column-source"><?php esc_html_e( 'Source URL', 'viable-url-media-uploader' ); ?></th>
								<th scope="col" class="column-date"><?php esc_html_e( 'Imported', 'viable-url-media-uploader' ); ?></th>
								<th scope="col" class="column-actions"><?php esc_html_e( 'Actions', 'viable-url-media-uploader' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $sample_rows as $row ) : ?>
								<tr>
									<td class="column-preview">
										<span class="vumu-pro-import-upsell__thumb"></span>
									</td>
									<td class="column-file">
										<strong><?php echo esc_html( $row['file'] ); ?></strong>
										<span class="vumu-import-log__mime"><?php esc_html_e( 'image', 'viable-url-media-uploader' ); ?></span>
									</td>
									<td class="column-source">
										<span class="vumu-import-log__source-url"><?php echo esc_html( $row['source'] ); ?></span>
										<span class="vumu-import-log__copy vumu-import-log__muted"><?php esc_html_e( 'Copy', 'viable-url-media-uploader' ); ?></span>
									</td>
									<td class="column-date">
										<time class="vumu-import-log__time"><?php echo esc_html( $row['date'] ); ?></time>
									</td>
									<td class="column-actions">
										<span class="vumu-import-log__muted"><?php esc_html_e( 'Edit', 'viable-url-media-uploader' ); ?></span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="vumu-pro-import-upsell__cta vumu-surface-card">
				<p class="vumu-pro-import-upsell__cta-title">
					<?php esc_html_e( 'Available in VUMU Pro', 'viable-url-media-uploader' ); ?>
				</p>
				<button
					type="button"
					class="vumu-pro-import-upsell__btn vumu-pro-upsell-import-btn"
					data-vumu-upgrade-url="<?php echo esc_url( $upgrade_url ); ?>"
				>
					<span class="vumu-pro-import-upsell__btn-label">
						<?php esc_html_e( 'Unlock URL import log', 'viable-url-media-uploader' ); ?>
					</span>
					<span class="vumu-pro-import-upsell__btn-badge"><?php esc_html_e( 'PRO', 'viable-url-media-uploader' ); ?></span>
				</button>
				<p class="vumu-pro-upsell-message">
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: upgrade link */
							__( 'Pro saves the original link on every import so you can audit, copy, and revisit sources anytime. %s', 'viable-url-media-uploader' ),
							'<a href="' . esc_url( $upgrade_url ) . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Get VUMU Pro →', 'viable-url-media-uploader' ) . '</a>'
						)
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}
}
