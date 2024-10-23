<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing price and currency.
 * This function registers the meta box in the admin interface for the 'classified' post type.
 *
 * @return void
 */
function classifieds_add_price_currency_meta_box() {
    add_meta_box(
        'classified_price_currency_box',  // Unique ID for the meta box
        'Precio y Moneda', // Title of the meta box
        'classifieds_price_currency_box_html', // Callback function to display the content of the meta box
        'classified',                     // Post type where the meta box will appear
        'normal',                         // Context (normal, side, etc.)
        'high'                          // Priority
    );
}

add_action( 'add_meta_boxes', 'classifieds_add_price_currency_meta_box' );

// Callback function to display both Price and Currency fields in a single meta box
function classifieds_price_currency_box_html( $post ) {
    // Retrieve stored values
    $price = get_post_meta( $post->ID, '_classified_price', true );
    $currency = get_post_meta( $post->ID, '_classified_currency', true );

    // If no currency is selected, default to empty string
    if ( empty( $currency ) ) {
        $currency = '';
    }

    ?>
    <label for="classified_price_field">Precio: </label>
    <input type="text" id="classified_price_field" name="classified_price_field" value="<?php echo esc_attr( $price ); ?>" />
    <br><br>

    <label>Moneda: </label><br>
    <input type="radio" id="currency_ars" name="classified_currency" value="ARS" <?php checked( $currency, 'ARS' ); ?> />
    <label for="currency_ars">Pesos Argentinos (ARS)</label><br>

    <input type="radio" id="currency_usd" name="classified_currency" value="USD" <?php checked( $currency, 'USD' ); ?> />
    <label for="currency_usd">Dólares (USD)</label><br>
    <?php
}

// Save the data from the Price and Currency fields when the post is saved
function classifieds_save_price_currency_meta( $post_id ) {
    // Check if the Price field is set
    if ( isset( $_POST['classified_price_field'] ) ) {
        $price_data = sanitize_text_field( $_POST['classified_price_field'] );
        update_post_meta( $post_id, '_classified_price', $price_data );
    }

    // Check if the Currency field is set
    if ( isset( $_POST['classified_currency'] ) ) {
        $currency_data = sanitize_text_field( $_POST['classified_currency'] );
        update_post_meta( $post_id, '_classified_currency', $currency_data );
    }
}

add_action( 'save_post', 'classifieds_save_price_currency_meta' );
?>
