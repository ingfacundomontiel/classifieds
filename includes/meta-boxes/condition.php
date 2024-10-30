<?php
/**
 * Plugin Name: Classifieds Plugin
 *
 * @package classifieds
 */

/**
 * Adds a meta box to the Classifieds post type for selecting the condition (Nuevo or Usado).
 *
 * @return void
 */
function classifieds_add_condition_meta_box() {
    add_meta_box(
        'classified_condition_box',        // Unique ID for the meta box
        'Condición',                       // Title of the meta box
        'classifieds_condition_box_html',  // Callback function to display the meta box content
        'classified',                      // Post type where the meta box will appear
        'normal',                          // Context (normal, side, etc.)
    );
}
add_action( 'add_meta_boxes', 'classifieds_add_condition_meta_box' );

/**
 * Callback function to display the fields for selecting condition in the meta box.
 *
 * @param WP_Post $post The current post object.
 */
function classifieds_condition_box_html( $post ) {
    // Retrieve the stored condition value.
    $condition = get_post_meta( $post->ID, '_classified_condition', true );
    ?>
    <input type="radio" id="condition_new" name="classified_condition" value="Nuevo" <?php checked( $condition, 'Nuevo' ); ?>>
    <label for="condition_new">Nuevo</label><br>

    <input type="radio" id="condition_used" name="classified_condition" value="Usado" <?php checked( $condition, 'Usado' ); ?>>
    <label for="condition_used">Usado</label><br>
    <?php
}

/**
 * Save the condition data when the Classified post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function classifieds_save_condition_meta( $post_id ) {
    // Check if the condition field is present in the POST data.
    if ( isset( $_POST['classified_condition'] ) ) {
        $condition = sanitize_text_field( $_POST['classified_condition'] );
        update_post_meta( $post_id, '_classified_condition', $condition );
    }
}
add_action( 'save_post', 'classifieds_save_condition_meta' );
