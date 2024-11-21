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

		<div class="classifieds-cta-container listing-page">
			<a href="https://ganaderiaynegocios.com/clasificados/carga-un-clasificado/" class="classifieds-cta">
				CARGÁ UN CLASIFICADO 
			</a>
		</div>
		
		<div class="classified-list-wrapper">
			<div class="container">
				<div class="row">
					<div class="col-12">
						<div class="classified-list">
							<?php
							while ( $query->have_posts() ) :
								$query->the_post();
								$terms    = wp_get_post_terms( get_the_ID(), 'classified_category' ); // Retrieve the classified categories.
								$price    = get_post_meta( get_the_ID(), '_classified_price', true ); // Retrieve the classified price.
								$currency = get_post_meta( get_the_ID(), '_classified_currency', true ); // Retrieve the classified currency.
								?>

								<a class="permalink classified" href="<?php echo esc_url( the_permalink() ); ?>">

									<div class="featured-image-wrapper">
										<?php
										if ( has_post_thumbnail() ) :
											$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
											?>
											<img class="featured-image" src="<?php echo esc_url( $image[0] ); ?>" alt="<?php the_title(); ?>">
											<?php
										else :
											?>
											<img class="featured-image" src="<?php echo esc_url( get_template_directory_uri() . '/images/placeholder.png' ); ?>" alt="<?php the_title(); ?>">
											<?php
										endif;
										?>
									</div>

									<?php if ( ! empty( $price ) ) : ?>
										<div class="price">
											<p><?php echo ( 'USD' === $currency ) ? 'USD$ ' : 'ARS$ ', esc_html( $price ); ?></p>
										</div>
									<?php endif; ?>

									<h4 class="title">
										<?php the_title(); ?>
									</h4>

									<div class="content">
										<?php
										echo esc_html( custom_trim_content( get_the_content() ) ); // Trim the content to 15 words.
										?>
									</div>

									<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) : ?>
										<div class="categories">
											<?php display_post_terms( get_the_ID(), 'classified_category' ); ?>
										</div>
									<?php endif; ?>

								</a>
								<?php
							endwhile;
							?>
						</div>
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
