<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing the Classified information (Price, Currency, Condition).
 *
 * @return void
 */
function classifieds_add_info_meta_box() {
    add_meta_box(
        'classified_info_box',  // Unique ID for the meta box
        'Información del Clasificado',      // Title of the meta box
        'classifieds_info_box_html', // Callback function to display the meta box content
        'classified',                   // Post type where the meta box will appear
        'normal',                       // Context (normal, side, etc.)
        'high'                          // Priority of the meta box
    );
}
add_action( 'add_meta_boxes', 'classifieds_add_info_meta_box' );

// Callback function to display the fields for Price, Currency, Condition in the same meta box
function classifieds_info_box_html( $post ) {

     // Retrieve stored values
     $price = get_post_meta( $post->ID, '_classified_price', true );
     $currency = get_post_meta( $post->ID, '_classified_currency', true );
     $condition = get_post_meta( $post->ID, '_classified_condition', true );
     $images_ids = get_post_meta( $post->ID, '_classified_images', true );

    // If no currency is selected, default to empty string
    if ( empty( $currency ) ) {
        $currency = '';
    }

    ?>
    <div class="price-currency-wrapper">
        <label for="classified_price_field">Precio: </label>
        <input type="text" id="classified_price_field" name="classified_price_field" value="<?php echo esc_attr( $price ); ?>" />
        <br><br>

        <label>Moneda: </label><br>
        <input type="radio" id="currency_ars" name="classified_currency" value="ARS" <?php checked( $currency, 'ARS' ); ?> />
        <label for="currency_ars">Pesos Argentinos (ARS)</label><br>

        <input type="radio" id="currency_usd" name="classified_currency" value="USD" <?php checked( $currency, 'USD' ); ?> />
        <label for="currency_usd">Dólares (USD)</label><br>
    </div>

    <div class="condition-wrapper">
        <label>Condición: </label><br>
        <input type="radio" id="condition_new" name="classified_condition" value="Nuevo" <?php checked( $condition, 'Nuevo' ); ?>>
        <label for="condition_new">Nuevo</label><br>

        <input type="radio" id="condition_used" name="classified_condition" value="Usado" <?php checked( $condition, 'Usado' ); ?>>
        <label for="condition_used">Usado</label><br>
    </div>

    <?php
}

// Save data when the Classified post is saved
function classifieds_save_info_meta( $post_id ) {

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

    // Check if the condition field is present in the POST data.
    if ( isset( $_POST['classified_condition'] ) ) {
        $condition = sanitize_text_field( $_POST['classified_condition'] );
        update_post_meta( $post_id, '_classified_condition', $condition );
    }
}

add_action( 'save_post', 'classifieds_save_info_meta' );