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
function classifieds_plugin_enqueue_styles() {
	wp_enqueue_style( 'bootstrap', plugin_dir_url( __FILE__ ) . '../dist/bootstrap.min.css' ); // Enqueue Bootstrap CSS.
	wp_enqueue_style( 'classifieds-styles', plugin_dir_url( __FILE__ ) . '../dist/main.min.css' ); // Enqueue the plugin's CSS file.
}
add_action( 'wp_enqueue_scripts', 'classifieds_plugin_enqueue_styles' ); // Hook the styles to be enqueued on the frontend.
