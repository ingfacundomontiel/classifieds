<?php
/**
 * This file contains functions to customize excerpts in WordPress.
 *
 * @package classifieds
 */

if ( ! function_exists( 'custom_excerpt_more' ) ) :
	/**
	 * Replaces the default excerpt more string with an ellipsis.
	 *
	 * @param string $more The string shown within the more link.
	 * @return string The new more string.
	 */
	function custom_excerpt_more( $more ) {
		return '…';
	}
endif;

add_filter( 'excerpt_more', 'custom_excerpt_more' );

if ( ! function_exists( 'custom_trim_content' ) ) :
	/**
	 * Trims the content of a post to 15 words.
	 *
	 * @param string $content The raw post content.
	 * @return string The trimmed content with 15 words.
	 */
	function custom_trim_content( $content ) {
		// Strip shortcodes and apply filters to the content.
		$content = strip_shortcodes( $content );
		$content = apply_filters( 'the_content', $content );
		$content = str_replace( ']]>', ']]&gt;', $content );

		// Trim the content to 15 words and add ellipsis if needed.
		return wp_trim_words( $content, 15, '…' );
	}
endif;
