<?php
/**
 * Function to display a custom Single Template for the Classifieds CPT.
 *
 * @package classifieds
 */

/**
 * Hook to load a custom template for the "classified" CPT from the plugin directory.
 * This function overrides the default template and loads the one located in the plugin.
 *
 * @param string $template The path to the template.
 * @return string The path to the custom template if it exists, or the original template.
 */
function classified_custom_template( $template ) {
	if ( is_singular( 'classified' ) ) {
		// Define the path to the custom template inside the plugin directory.
		$plugin_template = plugin_dir_path( __FILE__ ) . '../single-classified.php';

		// Check if the file exists, and if so, load it.
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}
	}

	// If not a classified CPT, return the original template.
	return $template;
}
add_filter( 'template_include', 'classified_custom_template' ); // Hook to load the custom template for Classifieds.
