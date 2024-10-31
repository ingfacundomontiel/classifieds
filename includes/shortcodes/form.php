<?php
/**
 * Display a classified form for users to submit new classified posts.
 *
 * @package classifieds
 */

// Include the classified fields configuration file.
// include_once plugin_dir_path( __DIR__ ) . '../classified-fields.php';

function display_classified_form() {
	if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['submit_classified'] ) ) {
		// Check if the nonce is set and valid.
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'submit_classified' ) ) {

			// Initialize an array to store validation errors.
			$form_errors = array();

			// Check required fields.
			if ( empty( $_POST['classified_title'] ) ) {
				$form_errors[] = 'El título es obligatorio.';
			}
			if ( empty( $_POST['classified_price'] ) ) {
				$form_errors[] = 'El precio es obligatorio.';
			}
			if ( empty( $_POST['classified_currency'] ) ) {
				$form_errors[] = 'Debes seleccionar una moneda.';
			}
			if ( empty( $_POST['classified_condition'] ) ) {
				$form_errors[] = 'Debes seleccionar una condición para el producto.';
			}
			if ( empty( $_POST['classified_location'] ) ) {
				$form_errors[] = 'La ubicación es obligatoria.';
			}
			if ( empty( $_POST['classified_category'] ) ) {
				$form_errors[] = 'Debes seleccionar al menos una categoría.';
			}
			
			if ( empty( $_POST['classified_email'] ) ) {
				$form_errors[] = 'El correo electrónico es obligatorio.';
			}
			if ( empty( $_POST['classified_user_type'] ) ) {
				$form_errors[] = 'Debes seleccionar si eres Productor o Comercio.';
			}
			

			// Display errors if any
			if ( ! empty( $form_errors ) ) {
				foreach ( $form_errors as $error ) {
					echo '<p style="color: red; background-color: #fffacd;">' . esc_html( $error ) . '</p>';
				}
			} else {
				// Sanitize and process the classified data if no errors.
				$classified_data = array(
					'post_title'   => sanitize_text_field( wp_unslash( $_POST['classified_title'] ) ),
					'post_content' => sanitize_textarea_field( wp_unslash( $_POST['classified_description'] ) ),
					'post_status'  => 'pending',
					'post_type'    => 'classified',
				);


				// Variables for Classified info.
				$classified_price     = floatval( wp_unslash( $_POST['classified_price'] ) );
				$classified_currency  = sanitize_text_field( wp_unslash( $_POST['classified_currency'] ) );
				$classified_condition = sanitize_text_field( wp_unslash( $_POST['classified_condition'] ) );
				$classified_location = sanitize_text_field( wp_unslash( $_POST['classified_location'] ) );

				// Variables for Contact info.
				$classified_email     = sanitize_email( wp_unslash( $_POST['classified_email'] ) );
				$classified_whatsapp  = sanitize_text_field( wp_unslash( $_POST['classified_whatsapp'] ) );
				$classified_user_type = sanitize_text_field( wp_unslash( $_POST['classified_user_type'] ) );

				// Insert the post.
				$classified_id = wp_insert_post( $classified_data );

				if ( $classified_id ) {
					// Assign the category.
					$categories = array_map( 'intval', wp_unslash( $_POST['classified_category'] ) );
					wp_set_post_terms( $classified_id, $categories, 'classified_category' );

					// Save custom fields.
					
					// Custom fields for Classified info.
					update_post_meta( $classified_id, '_classified_price', $classified_price );
					update_post_meta( $classified_id, '_classified_currency', $classified_currency );
					update_post_meta( $classified_id, '_classified_condition', $classified_condition );
					update_post_meta( $classified_id, '_classified_location', $classified_location );

					// Custom fields for Contact info.
					update_post_meta( $classified_id, '_classified_email', $classified_email );
					update_post_meta( $classified_id, '_classified_whatsapp', $classified_whatsapp );
					update_post_meta( $classified_id, '_classified_user_type', $classified_user_type );

					// Handle images.
					if ( ! empty( $_FILES['classified_images']['name'][0] ) ) {
						$image_ids   = array();
						$image_count = count( $_FILES['classified_images']['name'] );

						if ( $image_count > 5 ) {
							$image_count = 5;
						}

						for ( $i = 0; $i < $image_count; $i++ ) {
							$file = array(
								'name'     => $_FILES['classified_images']['name'][ $i ],
								'type'     => $_FILES['classified_images']['type'][ $i ],
								'tmp_name' => $_FILES['classified_images']['tmp_name'][ $i ],
								'error'    => $_FILES['classified_images']['error'][ $i ],
								'size'     => $_FILES['classified_images']['size'][ $i ],
							);

							$upload_overrides = array( 'test_form' => false );
							$movefile         = wp_handle_upload( $file, $upload_overrides );

							if ( $movefile && ! isset( $movefile['error'] ) ) {
								$attachment = array(
									'post_mime_type' => $movefile['type'],
									'post_title'     => sanitize_file_name( $movefile['file'] ),
									'post_content'   => '',
									'post_status'    => 'inherit',
								);

								$attachment_id = wp_insert_attachment( $attachment, $movefile['file'], $classified_id );
								require_once ABSPATH . 'wp-admin/includes/image.php';
								$attach_data = wp_generate_attachment_metadata( $attachment_id, $movefile['file'] );
								wp_update_attachment_metadata( $attachment_id, $attach_data );
								$image_ids[] = $attachment_id;
							}
						}

						if ( ! empty( $image_ids ) ) {
							update_post_meta( $classified_id, '_classified_images', $image_ids );
						}
					}

					echo '<p style="background-color: #fffacd;">¡Tu Clasificado se ha creado correctamente!</p>';
				} else {
					echo '<p style="background-color: #fffacd;">Hubo un error procesando tu Clasificado. Por favor, refresca la página e intentalo nuevamente.</p>';
				}
			}
		} else {
			// Nonce verification failed.
			echo '<p style="background-color: #fffacd;">Error de seguridad: El formulario no pudo ser procesado.</p>';
		}
	}

	$categories = get_terms(
		array(
			'taxonomy'    => 'classified_category',
			'hide_empty'  => false,
			'object_type' => array( 'classified' ),
		)
	);

	ob_start();
	?>

	<form id="classifiedForm" action="" method="post" enctype="multipart/form-data" class="classified-input-form">
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
			<label for="classified_currency">Moneda:</label>
			<div class="checkbox-wrapper">
				<input type="radio" id="currency_ars" name="classified_currency" value="ARS" required />
				<label for="currency_ars">Pesos Argentinos</label>
			</div>
			<div class="checkbox-wrapper">
				<input type="radio" id="currency_usd" name="classified_currency" value="USD" required />
				<label for="currency_usd">USD</label>
			</div>
		</div>

		<div class="classified-form-group">
			<label for="classified_condition">Condición:</label><br>

			<div class="checkbox-wrapper">
				<input type="radio" id="condition_new" name="classified_condition" value="Nuevo" required>
				<label for="condition_new">Nuevo</label><br>
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
			<label for="classified_images">Imágenes (hasta 5):</label>
			<input type="file" id="classified_images" name="classified_images[]" multiple="multiple" accept="image/*" />
		</div>

		<div class="classified-form-group">
			<label for="classified_category">Categoría</label>
			<?php
			foreach ( $categories as $category ) {
				?>
				<div class="checkbox-wrapper">
					<input type="checkbox" name="classified_category[]" value="<?php echo esc_attr( $category->term_id ); ?>">
					<label for="classified_category"><?php echo esc_html( $category->name ); ?></label>
				</div>
			<?php } ?>
		</div>

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
			<label>Soy:</label><br>

			<div class="checkbox-wrapper">
				<input type="radio" id="user_type_productor" name="classified_user_type" value="Productor" required>
				<label for="user_type_productor">Productor</label><br>
			</div>

			<div class="checkbox-wrapper">
				<input type="radio" id="user_type_comercio" name="classified_user_type" value="Comercio" required>
				<label for="user_type_comercio">Comercio</label>
			</div>
		</div>

		<div class="classified-form-group">
		
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
					<a href="https://ganaderiaynegocios.com/clasificados-terminos-y-condiciones/">Términos y Condiciones</a>
				</label>
			</div>

		</div>

		<div class="classified-form-group">
			<input type="submit" name="submit_classified" value="Enviar Clasificado">
		</div>
		
	</form>

	<?php
	return ob_get_clean();
}

add_shortcode( 'classified_form', 'display_classified_form' );
