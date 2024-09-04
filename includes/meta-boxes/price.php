<?php

// Add metabox for Classified Price field

function classifieds_add_custom_box() {
	add_meta_box(
		'classified_price_box',             // Unique ID.
		'Precio del Clasificado',           // Box title.
		'classifieds_price_box_html',       // Content callback, must be of type callable.
		'classified'                        // Post type.
	);
}

function classifieds_price_box_html( $post ) {
	$value = get_post_meta( $post->ID, '_classified_price', true );
	?>
	<label for="classified_price_field">Precio: </label>
	<input type="text" id="classified_price_field" name="classified_price_field" value="<?php echo esc_attr( $value ); ?>" />
	<?php
}

// Hook into the 'add_meta_boxes' action.
add_action( 'add_meta_boxes', 'classifieds_add_custom_box' );

// Make sure to save price field when Classified is saved.

function classifieds_save_postdata( $post_id ) {
	// Check if our nonce is set.
	if ( ! isset( $_POST['classified_price_field'] ) ) {
		return;
	}

	$price_data = sanitize_text_field( $_POST['classified_price_field'] );
	update_post_meta( $post_id, '_classified_price', $price_data );
}

// Hook into the 'save_post' action.
add_action( 'save_post', 'classifieds_save_postdata' );
