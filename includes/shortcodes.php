<?php
if ( ! function_exists( 'media_handle_upload' ) ) {
	require_once ABSPATH . 'wp-admin/includes/media.php';
}
if ( ! function_exists( 'wp_handle_upload' ) ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
}
if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
	require_once ABSPATH . 'wp-admin/includes/image.php';
}

require plugin_dir_path( __FILE__ ) . 'shortcodes/form.php';

require plugin_dir_path( __FILE__ ) . 'shortcodes/list.php';

require plugin_dir_path( __FILE__ ) . 'shortcodes/display-by-category.php';
