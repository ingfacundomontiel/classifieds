<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for capturing user type (Productor or Comercio).
 *
 * @return void
 */
function classifieds_add_user_type_meta_box() {
    add_meta_box(
        'classified_user_type_box',      // Unique ID for the meta box
        'Tipo de Usuario',               // Title of the meta box
        'classifieds_user_type_box_html', // Callback function to display the meta box content
        'classified',                    // Post type where the meta box will appear
        'normal',                        // Context (normal, side, etc.)
        'high'                           // Priority of the meta box
    );
}
add_action( 'add_meta_boxes', 'classifieds_add_user_type_meta_box' );

/**
 * Callback function to display the fields for User Type in the meta box
 *
 * @param WP_Post $post The current post object.
 * @return void
 */
function classifieds_user_type_box_html( $post ) {
    // Get the stored user type value
    $user_type = get_post_meta( $post->ID, '_classified_user_type', true );

    // Display radio buttons for user type selection
    ?>
    <label for="classified_user_type">Soy:</label><br>
    <input type="radio" id="productor" name="classified_user_type" value="Productor" <?php checked( $user_type, 'Productor' ); ?> />
    <label for="productor">Productor</label><br>

    <input type="radio" id="comercio" name="classified_user_type" value="Comercio" <?php checked( $user_type, 'Comercio' ); ?> />
    <label for="comercio">Comercio</label><br>
    <?php
}

/**
 * Save the User Type data when the Classified post is saved
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function classifieds_save_user_type_meta( $post_id ) {
    // Check if the User Type field is set and sanitize it
    if ( isset( $_POST['classified_user_type'] ) ) {
        $user_type = sanitize_text_field( wp_unslash( $_POST['classified_user_type'] ) );
        update_post_meta( $post_id, '_classified_user_type', $user_type );
    }
}

add_action( 'save_post', 'classifieds_save_user_type_meta' );
