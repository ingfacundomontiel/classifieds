<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing images.
 * This function registers the meta box in the admin interface for the 'classified' post type.
 *
 * @return void
 */
function classifieds_add_images_meta_box() {
	add_meta_box(
		'classified_images_box',            // Unique ID for the meta box.
		'Galería de Imágenes',               // Title for the meta box.
		'classifieds_images_box_html',      // Callback function to display the content of the meta box.
		'classified'                        // Post type where the meta box will appear.
	);
}

add_action( 'add_meta_boxes', 'classifieds_add_images_meta_box' ); // Hook to add the meta box to Classifieds.

/**
 * Callback function to display the image upload meta box in the admin interface.
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function classifieds_images_box_html( $post ) {
	// Get the stored image IDs.
	$images_ids = get_post_meta( $post->ID, '_classified_images', true );

	// Output the nonce for security purposes.
	wp_nonce_field( 'classifieds_save_images_nonce_action', 'classifieds_images_nonce' );

	// Button to upload images.
	echo '<input type="button" id="upload_images_button" class="button" value="Seleccionar Imágenes" />';
	echo '<input type="hidden" id="classified_images" name="classified_images" value="' . ( $images_ids ? esc_attr( implode( ',', $images_ids ) ) : '' ) . '" />';

	// Preview container for the selected images.
	echo '<div class="classified-images-admin" style="margin-top: 10px;">';
	if ( ! empty( $images_ids ) ) {
		foreach ( $images_ids as $attachment_id ) {
			// Display the existing images.
			$image_url = wp_get_attachment_image_src( $attachment_id, 'thumbnail' )[0];
			echo '<img src="' . esc_url( $image_url ) . '" style="margin-right: 10px; max-width: 100px;" />';
		}
	}
	echo '</div>';
}



/**
 * Save the images meta data when the Classified post is saved.
 * Verifies the nonce and sanitizes the input before saving the image IDs.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function classifieds_save_images( $post_id ) {
	// Verify the nonce before proceeding.
	if ( ! isset( $_POST['classifieds_images_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['classifieds_images_nonce'] ) ), 'classifieds_save_images_nonce_action' ) ) {
		return; // Exit if the nonce is invalid.
	}

	// Sanitize and save the image IDs.
	if ( isset( $_POST['classified_images'] ) ) {
		$images_ids = explode( ',', sanitize_text_field( wp_unslash( $_POST['classified_images'] ) ) );
		update_post_meta( $post_id, '_classified_images', $images_ids );
	}
}

add_action( 'save_post', 'classifieds_save_images' );


/**
 * Enqueue the media uploader script for the Classifieds image meta box.
 * This function includes the necessary scripts for uploading images through the WordPress media uploader.
 *
 * @param string $hook The current admin page hook.
 * @return void
 */
function classifieds_enqueue_media_uploader( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}

	// Enqueue the WordPress media uploader scripts .
	wp_enqueue_media();
	wp_enqueue_script( 'classifieds-images-admin', plugin_dir_url( __FILE__ ) . '../assets/js/admin-images.js', array( 'jquery' ), null, true );
}
add_action( 'admin_enqueue_scripts', 'classifieds_enqueue_media_uploader' ); // Hook to enqueue the media uploader scripts.
