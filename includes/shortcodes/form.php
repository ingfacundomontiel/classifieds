<?php
/**
 * Display a classified form for users to submit new classified posts.
 *
 * @package classifieds
 */

/**
 * Displays a form for users to submit new Classified posts.
 */
function display_classified_form() {

	$categories = get_terms(
		array(
			'taxonomy'    => 'classified_category',
			'hide_empty'  => false,
			'object_type' => array( 'classified' ),
		)
	);

	ob_start();
	?>

	<form id="classifiedForm" action="" method="post" enctype="multipart/form-data" class="classified-input-form" data-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
		<?php wp_nonce_field( 'submit_classified' ); ?>

		<div class="classified-form-group">
			<label for="classified_title">Título</label>
			<input type="text" id="classified_title" name="classified_title" required>
		</div>

		<div class="classified-form-group">
			<label for="classified_description">Descripción</label>
			<textarea id="classified_description" name="classified_description" required></textarea>
		</div>

		<div class="classified-form-group">
			<label for="classified_price">Precio</label>
			<input type="number" id="classified_price" name="classified_price" required>
		</div>

		<div class="classified-form-group">
			<label for="classified_currency">Moneda</label>
			<div class="checkbox-wrapper">
				<input type="radio" id="currency_ars" name="classified_currency" value="ARS" required>
				<label for="currency_ars">Pesos Argentinos</label>
			</div>
			<div class="checkbox-wrapper">
				<input type="radio" id="currency_usd" name="classified_currency" value="USD" required>
				<label for="currency_usd">USD</label>
			</div>
		</div>

		<div class="classified-form-group">
			<label for="classified_condition">Condición</label>
			<div class="checkbox-wrapper">
				<input type="radio" id="condition_new" name="classified_condition" value="Nuevo" required>
				<label for="condition_new">Nuevo</label>
			</div>
			<div class="checkbox-wrapper">
				<input type="radio" id="condition_used" name="classified_condition" value="Usado" required>
				<label for="condition_used">Usado</label>
			</div>
		</div>

		<div class="classified-form-group">
			<label for="classified_location">Localidad</label>
			<input type="text" id="classified_location" name="classified_location" required>
		</div>

		<div class="classified-form-group">
			<label class="mb-0" for="classified_images">Imágenes</label>
			<p class="info-text mt-0">(hasta 5, tamaño máximo: 1MB)</p>
			<input type="file" id="classified_images" name="classified_images[]" multiple="multiple" accept="image/*">
		</div>

		<div class="classified-form-group">
			<label for="classified_category">Categoría</label>
			<?php foreach ( $categories as $category ) { ?>
				<div class="checkbox-wrapper">
					<input type="checkbox" id="category_<?php echo esc_attr( $category->term_id ); ?>" name="classified_category[]" value="<?php echo esc_attr( $category->term_id ); ?>">
					<label for="category_<?php echo esc_attr( $category->term_id ); ?>"><?php echo esc_html( $category->name ); ?></label>
				</div>
			<?php } ?>
		</div>

		<div class="classified-contact-info-group">
			<p class="form-group-title">Información de Contacto</p>
			<div class="classified-form-group">
				<label for="classified_email">Correo electrónico</label>
				<input type="email" id="classified_email" name="classified_email" required>
			</div>

			<div class="classified-form-group">
				<label for="classified_whatsapp">Número de WhatsApp:</label>
				<input type="text" id="classified_whatsapp" name="classified_whatsapp" placeholder="Ej: 5491166667777">
				<p class="field-description whatsapp-field">Introduce el número de WhatsApp con código de país, sin espacios ni guiones. Ejemplo: 5491166667777</p>
			</div>

			<div class="classified-form-group classified-user-type-wrapper">
				<label for="classified_user_type">Soy:</label>
				<div class="checkbox-wrapper">
					<input type="radio" id="user_type_productor" name="classified_user_type" value="Productor" required>
					<label for="user_type_productor">Productor</label>
				</div>
				<div class="checkbox-wrapper">
					<input type="radio" id="user_type_comercio" name="classified_user_type" value="Comercio" required>
					<label for="user_type_comercio">Comercio</label>
				</div>
			</div>
		</div>

		<div class="classified-form-group consent-group">
			<div class="checkbox-wrapper">
				<input type="checkbox" id="newsletter_subscription" name="newsletter_subscription">
				<label for="newsletter_subscription">
					Acepto recibir comunicaciones de Ganadería y Negocios a través de email y/o WhatsApp.
				</label>
			</div>
			<div class="checkbox-wrapper">
				<input type="checkbox" id="classified_posting_consent" name="classified_posting_consent" required>
				<label for="classified_posting_consent">
					Doy mi consentimiento para publicar este anuncio con vigencia por 30 días y brindar mis datos de contacto sólo a los fines de esta publicación.
				</label>
			</div>
			<div class="checkbox-wrapper">
				<input type="checkbox" id="terms_conditions" name="terms_conditions" required>
				<label for="terms_conditions">
					Acepto los 
					<a class="terms-conditions" target="_blank" href="https://ganaderiaynegocios.com/clasificados-terminos-y-condiciones/">Términos y Condiciones</a>
				</label>
			</div>
		</div>

		<div class="classified-form-group">
			<input type="submit" name="submit_classified" value="Enviar Clasificado">
		</div>

		<div id="loader"></div>
	</form>

	<?php
	return ob_get_clean();
}

add_shortcode( 'classified_form', 'display_classified_form' );
