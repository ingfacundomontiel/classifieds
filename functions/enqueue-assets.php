<?php
/**
 * Function to enqueue the Classifieds plugin's stylesheets.
 *
 * @package classifieds
 */

/**
 * Enqueue the Classifieds plugin's stylesheets.
 *
 * @return void
 */
function classifieds_plugin_enqueue_styles()
{

	if ( is_singular('classified' )) {
		wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . '../dist/bootstrap.min.js', array('jquery'), '5.0.0', true); // Enqueue Bootstrap JS.
		wp_enqueue_script('main', plugin_dir_url(__FILE__) . '../dist/main.min.js', array('jquery'), '5.0.0', true); // Enqueue Bootstrap JS.

		wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . '../dist/bootstrap.min.css'); // Enqueue Bootstrap CSS.
		wp_enqueue_style('classifieds-styles', plugin_dir_url(__FILE__) . '../dist/main.min.css'); // Enqueue the plugin's CSS file.
	}

	// Check if the page contains the classifieds shortcode.
    global $post;

    if ( isset($post->post_content ) && ( has_shortcode( $post->post_content, 'classifieds_list' )  || has_shortcode( $post->post_content, 'classified_form' ) ) ) {
        wp_enqueue_script('bootstrap', plugin_dir_url(__FILE__) . '../dist/bootstrap.min.js', array('jquery'), '5.0.0', true); // Enqueue Bootstrap JS.
		wp_enqueue_script('main', plugin_dir_url(__FILE__) . '../dist/main.min.js', array('jquery'), '5.0.0', true); // Enqueue Bootstrap JS.

		wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . '../dist/bootstrap.min.css'); // Enqueue Bootstrap CSS.
		wp_enqueue_style('classifieds-styles', plugin_dir_url(__FILE__) . '../dist/main.min.css'); // Enqueue the plugin's CSS file.
    }
}
add_action('wp_enqueue_scripts', 'classifieds_plugin_enqueue_styles'); // Hook the styles to be enqueued on the frontend.
