<?php
/**
 * Upload Page Template
 *
 * @package ahsangadit\viable_url_media_uploader\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Correct asset path.
$assets_url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/image/';

$error = get_transient( 'vumu_upload_error' );
$success = get_transient( 'vumu_upload_success' );

if ( $error ) {
	delete_transient( 'vumu_upload_error' );
}

if ( $success ) {
	delete_transient( 'vumu_upload_success' );
}
?>

<div class="wrap">

	<!-- ===== HEADER SECTION ===== -->
	<div class="vumu-header">
		<div class="vumu-logo">
			<img src="<?php echo esc_url( $assets_url . 'main_logo.svg' ); ?>" alt="<?php esc_attr_e( 'Plugin Logo', 'viable-url-media-uploader' ); ?>" width="65" height="65">
			<h1><?php esc_html_e( 'Viable URL Media Uploader', 'viable-url-media-uploader' ); ?></h1>
		</div>
	</div>

	<div class="vumu-page-layout">
		<div class="vumu-main-content">

	<!-- ===== UPLOAD BOX ===== -->
	<div class="vumu-upload-box">
		<h2><?php esc_html_e( 'Upload from URL', 'viable-url-media-uploader' ); ?></h2>

		<!-- Custom Message Area -->
		<?php if ( ! empty( $success ) ) : ?>
			<div class="vumu-message vumu-message-success">
				<div class="vumu-message-icon">✅</div>
				<div class="vumu-message-content">
					<strong><?php esc_html_e( 'Upload Complete', 'viable-url-media-uploader' ); ?></strong>
					<p><?php echo esc_html( $success ); ?></p>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $error ) ) : ?>
			<div class="vumu-message vumu-message-error">
				<div class="vumu-message-icon">⚠️</div>
				<div class="vumu-message-content">
					<strong><?php esc_html_e( 'Upload Error', 'viable-url-media-uploader' ); ?></strong>
					<p><?php echo esc_html( $error ); ?></p>
				</div>
			</div>
		<?php endif; ?>

		<form method="post" action="<?php echo esc_url( admin_url( 'upload.php?page=vumu-upload-from-url' ) ); ?>" id="vumu-url-upload-form">
			<?php wp_nonce_field( 'vumu_upload_nonce', 'vumu_nonce' ); ?>

			<?php if ( vumu_is_pro_active() ) : ?>
				<?php do_action( 'vumu_render_pro_upload_form' ); ?>
			<?php else : ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="vumu-url-input"><?php esc_html_e( 'File URL', 'viable-url-media-uploader' ); ?></label>
							</th>
							<td>
								<input
									type="url"
									name="vumu_url"
									id="vumu-url-input"
									class="regular-text"
									placeholder="<?php esc_attr_e( 'Enter Image URL', 'viable-url-media-uploader' ); ?>"
									required
									autofocus
								/>
								<p class="description">
									<?php esc_html_e( 'Enter the full URL of the file you want to add to your media library.', 'viable-url-media-uploader' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>

				<?php
				require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-pro-upsell.php';
				\ahsangadit\viable_url_media_uploader\VUMU_Pro_Upsell::render();
				?>
			<?php endif; ?>

			<p class="submit">
				<button type="submit" class="button button-primary button-large" id="vumu-upload-btn">
					<?php
					if ( vumu_is_pro_active() ) {
						esc_html_e( 'Upload All Files', 'viable-url-media-uploader' );
					} else {
						esc_html_e( 'Upload File', 'viable-url-media-uploader' );
					}
					?>
				</button>
			</p>
		</form>
	</div>

	<!-- ===== INFO SECTION ===== -->
	<div class="vumu-info-section">

		<!-- Card 1 -->
		<div class="vumu-card">
			<div class="vumu-icon-circle">
				<img src="<?php echo esc_url( $assets_url . 'How-to-use.svg' ); ?>" alt="<?php esc_attr_e( 'How to use', 'viable-url-media-uploader' ); ?>" width="40" height="40">
			</div>
			<h3><?php esc_html_e( 'How to use', 'viable-url-media-uploader' ); ?></h3>
			<ol>
				<li><?php esc_html_e( 'Find the URL of the file you want to upload.', 'viable-url-media-uploader' ); ?></li>
				<li><?php esc_html_e( 'Paste the URL into the input field above.', 'viable-url-media-uploader' ); ?></li>
				<li><?php esc_html_e( 'Click the “Upload File” button.', 'viable-url-media-uploader' ); ?></li>
				<li><?php esc_html_e( 'The file will be downloaded and added to your media library.', 'viable-url-media-uploader' ); ?></li>
			</ol>
		</div>

		<!-- Card 2 -->
		<div class="vumu-card">
			<div class="vumu-icon-circle">
				<img src="<?php echo esc_url( $assets_url . 'Supported.svg' ); ?>" alt="<?php esc_attr_e( 'Supported File Types', 'viable-url-media-uploader' ); ?>" width="40" height="40">
			</div>
			<h3><?php esc_html_e( 'Supported file types', 'viable-url-media-uploader' ); ?></h3>
			<p><?php esc_html_e( 'This plugin supports all file types that WordPress can handle:', 'viable-url-media-uploader' ); ?></p>
			<ul>
				<li><strong><?php esc_html_e( 'Images:', 'viable-url-media-uploader' ); ?></strong> JPG, PNG, GIF, WebP, SVG</li>
				<li><strong><?php esc_html_e( 'Videos:', 'viable-url-media-uploader' ); ?></strong> MP4, WebM, MOV</li>
				<li><strong><?php esc_html_e( 'Documents:', 'viable-url-media-uploader' ); ?></strong> PDF, DOC, DOCX, XLS, XLSX</li>
				<li><strong><?php esc_html_e( 'Audio:', 'viable-url-media-uploader' ); ?></strong> MP3, WAV, OGG</li>
			</ul>
		</div>

	</div>

		</div><!-- .vumu-main-content -->

		<aside class="vumu-sidebar">
			<?php
			require_once VUMU_PLUGIN_DIR . 'includes/class-vumu-footer-boxes.php';
			\ahsangadit\viable_url_media_uploader\VUMU_Footer_Boxes::render();
			?>
		</aside>
	</div><!-- .vumu-page-layout -->
</div>