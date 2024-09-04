<?php

// Hook to add the meta box
function classifieds_add_currency_meta_box() {
	add_meta_box(
		'classified_currency_box',       // Unique ID
		'Moneda',                        // Meta box title
		'classifieds_currency_box_html', // Callback function to display the meta box
		'classified',                    // Post type
	);
}

add_action( 'add_meta_boxes', 'classifieds_add_currency_meta_box' );

// Callback function to show the currency box
function classifieds_currency_box_html( $post ) {
	// Get stored currency
	$currency = get_post_meta( $post->ID, '_classified_currency', true );

	// Display radio buttons
	?>	
	<input type="radio" id="currency_ars" name="classified_currency" value="ARS" <?php checked( $currency, 'ARS' ); ?> />
	<label for="currency_ars">Pesos Argentinos</label><br>
	
	<input type="radio" id="currency_usd" name="classified_currency" value="USD" <?php checked( $currency, 'USD' ); ?> />
	<label for="currency_usd">USD</label><br>
	<?php
}

// Hook to save the meta box data
function classifieds_save_currency_meta($post_id) {
    // Check if nonce is set
    if (isset($_POST['classified_currency'])) {
        $currency = sanitize_text_field($_POST['classified_currency']);
        update_post_meta($post_id, '_classified_currency', $currency);
    }
}

add_action('save_post', 'classifieds_save_currency_meta');