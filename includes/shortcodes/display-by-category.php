<?php
/**
 * Display classifieds by category
 *
 * @package classifieds
 */

// Display classifieds by category

function display_classifieds_by_category( $atts ) {
	$atts  = shortcode_atts( array( 'category' => '' ), $atts, 'classifieds_by_category' );
	$args  = array(
		'post_type'      => 'classified',
		'category_name'  => $atts['category'],
		'posts_per_page' => -1,
	);
	$query = new WP_Query( $args );

	ob_start();
	if ( $query->have_posts() ) {
		echo '<div class="classified-list">';
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="classified">
				<h2><?php the_title(); ?></h2>
				<div><?php the_content(); ?></div>
				<?php if ( has_post_thumbnail() ) { ?>
					<div><?php the_post_thumbnail(); ?></div>
				<?php } ?>
				<div><?php comments_template(); ?></div>
			</div>
			<?php
		}
		echo '</div>';
	} else {
		echo 'No classifieds found.';
	}
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'classifieds_by_category', 'display_classifieds_by_category' );
