<?php
/**
 * The template for displaying a single Classified post.
 *
 * @package classifieds
 */

get_header();
?>
<main id="primary" class="site-main single-page single-classified-content">
	<?php
	if ( have_posts() ) :

		while ( have_posts() ) :

			the_post();

			$price     = get_post_meta( get_the_ID(), '_classified_price', true );
			$currency  = get_post_meta( get_the_ID(), '_classified_currency', true );
			$image_ids = get_post_meta( get_the_ID(), '_classified_images', true );
			?>
			<section class="block the-content">
				<div class="container">
					<div class="row">
						<div class="col-12 col-lg-5 carousel-col">

							<?php
							if ( ! empty( $image_ids ) ) {
								?>
								<div id="classifiedCarousel" class="carousel slide" data-bs-ride="carousel">
									<div class="carousel-inner">
										<?php
										$active_class = 'active';
										foreach ( $image_ids as $image_id ) {
											?>
											<div class="carousel-item <?php echo esc_attr( $active_class ); ?>">
												<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'd-block w-100 classified-carousel-img' ) ); ?>
											</div>
											<?php
											$active_class = '';
										}
										?>
									</div>
									<button class="carousel-control-prev" type="button" data-bs-target="#classifiedCarousel" data-bs-slide="prev">
										<span class="carousel-control-prev-icon" aria-hidden="false"></span>
									</button>
									<button class="carousel-control-next" type="button" data-bs-target="#classifiedCarousel" data-bs-slide="next">
										<span class="carousel-control-next-icon" aria-hidden="false"></span>
									</button>
								</div>
								<?php
							}
							?>

						</div><!-- .carousel-col -->

						<div class="col-12 col-lg-6 content-col">
							<h1>
								<?php
								the_title();
								?>
							</h1>
							<div class="content">
								<?php the_content(); ?>
							</div>
							<?php
							if ( ! empty( $price ) ) {
								?>
								<div class="price">
									<p><strong>Precio: </strong><?php echo ( 'USD' === $currency ) ? 'USD$ ' : 'ARS$ ', esc_html( $price ); ?> </p>
								</div>
							<?php } ?>

							<div class="categories">
								<strong>Categorías: </strong>
								<?php
								display_post_terms( get_the_ID(), 'classified_category' );
								?>
							</div>

							<?php
							require plugin_dir_path( __FILE__ ) . 'includes/single/inquiry-form.php';
							?>

						</div> <!-- .content-col -->
					</div><!-- .row -->
				</div><!-- .container -->
			</section>
			<?php
		endwhile;
	endif;
	?>

</main><!-- #main -->

<?php
get_footer();
