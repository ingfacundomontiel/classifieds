<?php

// Hook to add the meta box
function classifieds_add_images_meta_box() {
	add_meta_box(
		'classified_images_box',            // Unique ID
		'Galería de Imágenes',               // Meta box title
		'classifieds_images_box_html',      // Callback function
		'classified',                        // Post type
	);
}

add_action( 'add_meta_boxes', 'classifieds_add_images_meta_box' );

// Callback function to show the images box
function classifieds_images_box_html( $post ) {
	// Get the stored image IDs
	$images_ids = get_post_meta( $post->ID, '_classified_images', true ); // Updated meta key

	// If no images are uploaded, show a message
	if ( empty( $images_ids ) ) {
		echo '<p>No hay imágenes asociadas a este Clasificado.</p>';
	} else {
		echo '<div class="classified-images-admin">';
		foreach ( $images_ids as $attachment_id ) {
			// Get the image URL and display it
			$image_url = wp_get_attachment_image_src( $attachment_id, 'thumbnail' )[0];
			echo '<img src="' . esc_url( $image_url ) . '" style="margin-right: 10px; max-width: 100px;" />';
		}
		echo '</div>';
	}

	// Button to upload images
	// echo '<input type="button" id="upload_images_button" class="button" value="Upload Images" />';
	// echo '<input type="hidden" id="classified_images" name="classified_images" value="' . esc_attr( implode( ',', $images_ids ) ) . '" />';
}

// Save the images when the Classified is saved
function classifieds_save_images( $post_id ) {
	if ( isset( $_POST['classified_images'] ) ) {
		$images_ids = explode( ',', sanitize_text_field( $_POST['classified_images'] ) );
		update_post_meta( $post_id, '_classified_images', $images_ids ); // Same meta key for images
	}
}

add_action( 'save_post', 'classifieds_save_images' );

// Enqueue the media uploader scripts
// function classifieds_enqueue_media_uploader( $hook ) {
// if ( 'post.php' != $hook && 'post-new.php' != $hook ) {
// return;
// }

// wp_enqueue_media();
// wp_enqueue_script( 'classifieds-images-admin', plugin_dir_url( __FILE__ ) . 'js/admin-images.js', array( 'jquery' ), null, true );
// }
// add_action( 'admin_enqueue_scripts', 'classifieds_enqueue_media_uploader' );
