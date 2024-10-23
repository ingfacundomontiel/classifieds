<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing contact information (Email and WhatsApp).
 *
 * @return void
 */
function classifieds_add_contact_info_meta_box() {
    add_meta_box(
        'classified_contact_info_box',  // Unique ID for the meta box
        'Información de Contacto',      // Title of the meta box
        'classifieds_contact_info_box_html', // Callback function to display the meta box content
        'classified',                   // Post type where the meta box will appear
        'normal',                       // Context (normal, side, etc.)
        'high'                          // Priority of the meta box
    );
}
add_action( 'add_meta_boxes', 'classifieds_add_contact_info_meta_box' );

// Callback function to display the fields for Email and WhatsApp in the same meta box
function classifieds_contact_info_box_html( $post ) {
    // Get the stored email and WhatsApp values
    $email = get_post_meta( $post->ID, '_classified_email', true );
    $whatsapp_number = get_post_meta( $post->ID, '_classified_whatsapp', true );

    // Display the email field
    ?>
    <label for="classified_email">Correo electrónico:</label>
    <input type="email" name="classified_email" value="<?php echo esc_attr( $email ); ?>" size="25" />
    <br><br>
    <?php

    // Display the WhatsApp field
    ?>
    <label for="classified_whatsapp">Número de WhatsApp:</label>
    <input type="text" id="classified_whatsapp" name="classified_whatsapp" value="<?php echo esc_attr( $whatsapp_number ); ?>" size="25" />
    <p class="description">Introduce el número de WhatsApp con código de país, sin espacios ni guiones. Ejemplo: 5491166667777</p>
    <?php
}

// Save the Email and WhatsApp data when the Classified post is saved
function classifieds_save_contact_info_meta( $post_id ) {
    // Save Email
    if ( isset( $_POST['classified_email'] ) ) {
        $email = sanitize_email( $_POST['classified_email'] );
        if ( is_email( $email ) ) {
            update_post_meta( $post_id, '_classified_email', $email );
        } else {
            delete_post_meta( $post_id, '_classified_email' ); // If invalid, remove the entry
        }
    }

    // Save WhatsApp
    if ( isset( $_POST['classified_whatsapp'] ) ) {
        $whatsapp_number = sanitize_text_field( $_POST['classified_whatsapp'] );
        update_post_meta( $post_id, '_classified_whatsapp', $whatsapp_number );
    }
}

add_action( 'save_post', 'classifieds_save_contact_info_meta' );
?>
