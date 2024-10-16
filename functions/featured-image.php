<?php

/**
 * Sets the first gallery image as the featured image if no featured image is set.
 *
 * @param int $post_id The ID of the post being saved.
 * @return void
 */
function set_featured_image_from_gallery($post_id)
{
    // Ensure it's the right post type and prevent autosave.
    if (get_post_type($post_id) !== 'classified' || wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

    // Check if the post already has a featured image.
    if (has_post_thumbnail($post_id)) {
        return; // If it already has a featured image, we don't need to do anything.
    }

    // Get the gallery images.
    $images_ids = get_post_meta($post_id, '_classified_images', true);

    // If there are images in the gallery, set the first one as the featured image.
    if (! empty($images_ids) && is_array($images_ids)) {
        $first_image_id = $images_ids[0]; // Get the first image ID from the gallery.
        set_post_thumbnail($post_id, $first_image_id); // Set the first image as the featured image.
    }
}

// Hook into the 'save_post' action to trigger the function when a Classified is saved or updated.
add_action('save_post', 'set_featured_image_from_gallery');
