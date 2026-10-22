<?php
/**
 * Upload Page Template
 *
 * @package ahsangadit\viable_url_media_uploader\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$assets_url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/image/';

require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-upload-handler.php';

$error_key   = \ahsangadit\viable_url_media_uploader\VUMU_Upload_Handler::get_error_transient_key();
$success_key = \ahsangadit\viable_url_media_uploader\VUMU_Upload_Handler::get_success_transient_key();

$error   = get_transient( $error_key );
$success = get_transient( $success_key );

if ( $error ) {
	delete_transient( $error_key );
}

if ( $success ) {
	delete_transient( $success_key );
}

$has_notice = ! empty( $success ) || ! empty( $error );

$vumu_active_tab = 'upload';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Tab navigation only.
if ( isset( $_GET['vumu_tab'] ) && 'imports' === sanitize_key( wp_unslash( $_GET['vumu_tab'] ) ) ) {
	$vumu_active_tab = 'imports';
}

$vumu_upload_tab_url = admin_url( 'upload.php?page=vumu-upload-from-url' );
require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-pro-upsell.php';
$vumu_imports_tab_url = \ahsangadit\viable_url_media_uploader\VUMU_Pro_Upsell::get_imports_tab_url();
?>

<div class="wrap vumu-admin">

	<header class="vumu-header">
		<p class="vumu-header-eyebrow"><?php esc_html_e( 'Media → Upload from URL', 'viable-url-media-uploader' ); ?></p>
		<div class="vumu-logo">
			<img class="vumu-logo-mark" src="<?php echo esc_url( $assets_url . 'main_logo.svg' ); ?>" alt="" width="34" height="34">
			<h1 class="vumu-logo-title"><?php esc_html_e( 'Viable URL Media Uploader', 'viable-url-media-uploader' ); ?></h1>
		</div>
	</header>

	<?php
	$vumu_pro_needs_license = class_exists( '\ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin' )
		&& \ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin::should_show_license_ui()
		&& \ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin::needs_license();
	$vumu_parent_ready        = ! $vumu_pro_needs_license
		|| \ahsangadit\viable_url_media_uploader_pro\VUMU_Pro_License_Admin::is_parent_ready_for_pro_license();
	?>
	<?php if ( $vumu_pro_needs_license ) : ?>
		<div class="notice notice-warning vumu-pro-license-notice" style="margin: 16px 0 20px;">
			<?php if ( ! $vumu_parent_ready ) : ?>
				<p>
					<strong><?php esc_html_e( 'Complete free plugin setup first', 'viable-url-media-uploader' ); ?></strong><br>
					<?php esc_html_e( 'Finish the Viable URL Media Uploader (free) Freemius opt-in on this page before activating your Pro license.', 'viable-url-media-uploader' ); ?>
				</p>
			<?php else : ?>
				<p>
					<strong><?php esc_html_e( 'Activate your Pro license', 'viable-url-media-uploader' ); ?></strong><br>
					<?php esc_html_e( 'Viable URL Media Uploader Pro is installed but your license is not active on this site yet.', 'viable-url-media-uploader' ); ?>
					<?php
					$vumup_fs = function_exists( 'vumup_fs' ) ? vumup_fs() : null;
					$affix    = ( $vumup_fs && is_object( $vumup_fs ) ) ? $vumup_fs->get_unique_affix() : '';
					?>
					<a href="#" class="button button-primary activate-license-trigger <?php echo esc_attr( $affix ); ?>" style="margin-top: 8px;">
						<?php esc_html_e( 'Enter license key', 'viable-url-media-uploader' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="vumu-page-layout">
		<div class="vumu-main-content">

			<section class="vumu-upload-box vumu-surface-card" aria-labelledby="vumu-upload-heading">
				<div class="vumu-upload-box__header">
					<nav class="vumu-media-tabs" aria-label="<?php esc_attr_e( 'Media sections', 'viable-url-media-uploader' ); ?>">
						<?php if ( 'upload' === $vumu_active_tab ) : ?>
							<span class="vumu-media-tab is-active" aria-current="page">
								<?php esc_html_e( 'Upload from URL', 'viable-url-media-uploader' ); ?>
							</span>
						<?php else : ?>
							<a class="vumu-media-tab" href="<?php echo esc_url( $vumu_upload_tab_url ); ?>">
								<?php esc_html_e( 'Upload from URL', 'viable-url-media-uploader' ); ?>
							</a>
						<?php endif; ?>

						<?php if ( 'imports' === $vumu_active_tab ) : ?>
							<span class="vumu-media-tab is-active" aria-current="page">
								<?php esc_html_e( 'URL imports', 'viable-url-media-uploader' ); ?>
								<?php if ( ! vumu_is_pro_active() ) : ?>
									<span class="vumu-media-tab-pro-badge"><?php esc_html_e( 'PRO', 'viable-url-media-uploader' ); ?></span>
								<?php endif; ?>
							</span>
						<?php else : ?>
							<a class="vumu-media-tab" href="<?php echo esc_url( $vumu_imports_tab_url ); ?>">
								<?php esc_html_e( 'URL imports', 'viable-url-media-uploader' ); ?>
								<?php if ( ! vumu_is_pro_active() ) : ?>
									<span class="vumu-media-tab-pro-badge"><?php esc_html_e( 'PRO', 'viable-url-media-uploader' ); ?></span>
								<?php endif; ?>
							</a>
						<?php endif; ?>

						<a class="vumu-media-tab" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>">
							<?php esc_html_e( 'Media Library', 'viable-url-media-uploader' ); ?>
						</a>
					</nav>
					<div class="vumu-upload-box-heading">
						<h2 id="vumu-upload-heading">
							<?php
							if ( 'imports' === $vumu_active_tab ) {
								esc_html_e( 'URL imports', 'viable-url-media-uploader' );
							} else {
								esc_html_e( 'Upload from URL', 'viable-url-media-uploader' );
							}
							?>
						</h2>
						<?php if ( vumu_is_pro_active() ) : ?>
							<span class="vumu-pro-badge"><?php esc_html_e( 'VUMU PRO', 'viable-url-media-uploader' ); ?></span>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( 'imports' === $vumu_active_tab && vumu_is_pro_active() ) : ?>
					<?php do_action( 'vumu_render_pro_import_log_tab' ); ?>
				<?php elseif ( 'imports' === $vumu_active_tab ) : ?>
					<?php \ahsangadit\viable_url_media_uploader\VUMU_Pro_Upsell::render_import_log(); ?>
				<?php else : ?>

				<?php if ( $has_notice ) : ?>
					<div class="vumu-upload-box__notices" aria-live="polite" aria-atomic="true">
						<?php if ( ! empty( $success ) ) : ?>
							<div class="vumu-message vumu-message-success" role="status">
								<span class="vumu-message-icon vumu-message-icon--success" aria-hidden="true"></span>
								<div class="vumu-message-content">
									<strong><?php esc_html_e( 'Upload Complete', 'viable-url-media-uploader' ); ?></strong>
									<p><?php echo esc_html( $success ); ?></p>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $error ) ) : ?>
							<div class="vumu-message vumu-message-error" role="alert">
								<span class="vumu-message-icon vumu-message-icon--error" aria-hidden="true"></span>
								<div class="vumu-message-content">
									<strong><?php esc_html_e( 'Upload Error', 'viable-url-media-uploader' ); ?></strong>
									<p><?php echo esc_html( $error ); ?></p>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo esc_url( admin_url( 'upload.php?page=vumu-upload-from-url' ) ); ?>" id="vumu-url-upload-form" class="vumu-upload-box__body">
					<?php wp_nonce_field( 'vumu_upload_nonce', 'vumu_nonce' ); ?>

					<?php if ( vumu_is_pro_active() ) : ?>
						<?php do_action( 'vumu_render_pro_upload_form' ); ?>
					<?php else : ?>
						<div class="vumu-field">
							<label class="vumu-field-label" for="vumu-url-input"><?php esc_html_e( 'File URL', 'viable-url-media-uploader' ); ?></label>
							<div class="vumu-input-wrap">
								<input
									type="url"
									name="vumu_url"
									id="vumu-url-input"
									class="vumu-url-input"
									placeholder="<?php esc_attr_e( 'https://example.com/image.jpg', 'viable-url-media-uploader' ); ?>"
									required
									autofocus
								/>
							</div>
							<p class="vumu-field-hint">
								<?php esc_html_e( 'Direct, publicly accessible file URL you have permission to import.', 'viable-url-media-uploader' ); ?>
							</p>
						</div>

						<?php
						require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-pro-upsell.php';
						\ahsangadit\viable_url_media_uploader\VUMU_Pro_Upsell::render();
						?>
					<?php endif; ?>

					<div class="vumu-upload-box__footer">
						<button type="submit" class="button button-primary button-large" id="vumu-upload-btn">
							<?php
							if ( vumu_is_pro_active() ) {
								esc_html_e( 'Upload All Files', 'viable-url-media-uploader' );
							} else {
								esc_html_e( 'Upload File', 'viable-url-media-uploader' );
							}
							?>
						</button>
					</div>
				</form>

				<?php endif; ?>
			</section>

			<?php if ( 'upload' === $vumu_active_tab ) : ?>
			<div class="vumu-info-section">
				<div class="vumu-card vumu-surface-card">
					<div class="vumu-icon-circle">
						<img src="<?php echo esc_url( $assets_url . 'How-to-use.svg' ); ?>" alt="" width="40" height="40">
					</div>
					<h3><?php esc_html_e( 'How to use', 'viable-url-media-uploader' ); ?></h3>
					<ol class="vumu-steps">
						<li><?php esc_html_e( 'Find the URL of the file you want to upload.', 'viable-url-media-uploader' ); ?></li>
						<li><?php esc_html_e( 'Paste the URL into the input field above.', 'viable-url-media-uploader' ); ?></li>
						<li><?php esc_html_e( 'Click “Upload File”.', 'viable-url-media-uploader' ); ?></li>
						<li><?php esc_html_e( 'The file is added to your media library.', 'viable-url-media-uploader' ); ?></li>
					</ol>
				</div>

				<div class="vumu-card vumu-surface-card">
					<div class="vumu-icon-circle">
						<img src="<?php echo esc_url( $assets_url . 'Supported.svg' ); ?>" alt="" width="40" height="40">
					</div>
					<h3><?php esc_html_e( 'Supported file types', 'viable-url-media-uploader' ); ?></h3>
					<p class="vumu-card-lead"><?php esc_html_e( 'Any format WordPress can handle, including:', 'viable-url-media-uploader' ); ?></p>
					<ul class="vumu-type-chips" aria-label="<?php esc_attr_e( 'Supported formats', 'viable-url-media-uploader' ); ?>">
						<li><span class="vumu-chip"><?php esc_html_e( 'Images', 'viable-url-media-uploader' ); ?></span> JPG, PNG, GIF, WebP, SVG</li>
						<li><span class="vumu-chip"><?php esc_html_e( 'Video', 'viable-url-media-uploader' ); ?></span> MP4, WebM, MOV</li>
						<li><span class="vumu-chip"><?php esc_html_e( 'Documents', 'viable-url-media-uploader' ); ?></span> PDF, DOC, DOCX, XLS, XLSX</li>
						<li><span class="vumu-chip"><?php esc_html_e( 'Audio', 'viable-url-media-uploader' ); ?></span> MP3, WAV, OGG</li>
					</ul>
				</div>
			</div>
			<?php endif; ?>

		</div><!-- .vumu-main-content -->

		<aside class="vumu-sidebar" aria-label="<?php esc_attr_e( 'Plugin links', 'viable-url-media-uploader' ); ?>">
			<?php
			require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-footer-boxes.php';
			\ahsangadit\viable_url_media_uploader\VUMU_Footer_Boxes::render();
			?>
		</aside>
	</div><!-- .vumu-page-layout -->
</div>
