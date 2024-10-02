<?php
/**
 * Shortcode to display a list of classified posts.
 *
 * @package classifieds
 */

/**
 * Displays a list of classified posts using a custom WP_Query.
 * The function outputs a grid layout with classified details, including title, content, price, images, categories, and comments.
 *
 * @return string HTML content for the classifieds list.
 */
function display_classifieds_list() {
	$args  = array(
		'post_type'      => 'classified', // Custom post type "classified".
		'posts_per_page' => -1, // Retrieve all posts.
	);
	$query = new WP_Query( $args ); // Create a new query.

	ob_start(); // Start output buffering.

	if ( $query->have_posts() ) :
		?>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="classified-list">
						<?php
						while ( $query->have_posts() ) :
							$query->the_post();
							$price     = get_post_meta( get_the_ID(), '_classified_price', true ); // Retrieve the classified price.
							$currency  = get_post_meta( get_the_ID(), '_classified_currency', true ); // Retrieve the classified currency.
							$image_ids = get_post_meta( get_the_ID(), '_classified_images', true ); // Retrieve associated images.
							?>

							<div class="classified">
								<h2><a href="<?php echo esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></h2>
								<div class="content">
									<?php the_content(); ?>
								</div>
								<?php if ( ! empty( $price ) ) : ?>
									<div class="price">
										<p><strong>Precio: </strong> <?php echo ( 'USD' === $currency ) ? 'USD$ ' : 'ARS$ ', esc_html( $price ); ?></p>
									</div>
								<?php endif; ?>

								<?php if ( ! empty( $image_ids ) ) : ?>
									<div class="classified-gallery">
										<div class="gallery-wrapper">
											<?php
											foreach ( $image_ids as $image_id ) {
												echo wp_get_attachment_image( $image_id, 'medium' ); // Display each image.
											}
											?>
										</div>
									</div>
								<?php endif; ?>

								<?php if ( has_term( '', 'classified_category' ) ) : ?>
									<div class="categories">
										<strong>Categorías: </strong>
										<?php echo get_the_term_list( get_the_ID(), 'classified_category', '', ', ', '' ); ?>
									</div>
								<?php endif; ?>

							</div>
							<?php
						endwhile;
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	else :
		echo 'No se encontraron Clasificados.'; // Display a message if no classifieds are found.
	endif;

	wp_reset_postdata(); // Restore original post data.
	return ob_get_clean(); // Return the buffered content.
}

add_shortcode( 'classifieds_list', 'display_classifieds_list' ); // Register shortcode.
