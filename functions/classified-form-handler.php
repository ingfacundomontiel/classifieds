<?php
/**
 * This file contains functions to handle the classified form submission.
 *
 * @package Classifieds
 */

/**
 * Process the classified form submission via AJAX.
 *
 * @return void
 */
function handle_classified_submission() {
	// Check if the nonce is set and valid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'submit_classified' ) ) {
		wp_send_json_error( 'Security error: form could not be processed.' );
	}

	// Verify that the request is an AJAX call.
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		wp_send_json_error( 'Invalid request.' );
	}

	// Check if it's a valid AJAX request.
	if ( ! isset( $_POST['action'] ) || 'submit_classified' !== $_POST['action'] ) {
		wp_send_json_error( 'Invalid submission method.' );
	}

	// Verify the header referer to protect against CSRF attacks.
	if ( ! isset( $_SERVER['HTTP_REFERER'] ) || strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_REFERER'] ) ), get_site_url() ) !== 0 ) {
		wp_send_json_error( 'Invalid referer.' );
	}

	// Verify the header origin to protect against CSRF attacks.
	if ( isset( $_SERVER['HTTP_ORIGIN'] ) && get_site_url() !== $_SERVER['HTTP_ORIGIN'] ) {
		wp_send_json_error( 'Invalid origin.' );
	}

	// Initialize an array to store validation errors.
	$form_errors = array();

	// Validate required fields.
	if ( ! isset( $_POST['classified_title'] ) || empty( $_POST['classified_title'] ) ) {
		$form_errors[] = 'The title is required.';
	}

	if ( ! isset( $_POST['classified_description'] ) || empty( $_POST['classified_description'] ) ) {
		$form_errors[] = 'The description is required.';
	}

	$classified_price = isset( $_POST['classified_price'] ) ? floatval( wp_unslash( $_POST['classified_price'] ) ) : '0';
	if ( empty( $classified_price ) ) {
		$form_errors[] = 'The price must be greater than zero.';
	}

	$allowed_currencies  = array( 'ARS', 'USD' );
	$classified_currency = isset( $_POST['classified_currency'] ) ? sanitize_text_field( wp_unslash( $_POST['classified_currency'] ) ) : 'ARS';
	if ( ! in_array( $classified_currency, $allowed_currencies, true ) ) {
		$form_errors[] = 'Invalid currency selected.';
	}

	$allowed_conditions   = array( 'Nuevo', 'Usado' );
	$classified_condition = isset( $_POST['classified_condition'] ) ? sanitize_text_field( wp_unslash( $_POST['classified_condition'] ) ) : 'Nuevo';
	if ( ! in_array( $classified_condition, $allowed_conditions, true ) ) {
		$form_errors[] = 'Invalid product condition selected.';
	}

	if ( ! isset( $_POST['classified_location'] ) || empty( $_POST['classified_location'] ) ) {
		$form_errors[] = 'The location is required.';
	}

	$categories = isset( $_POST['classified_category'] ) ? array_map( 'intval', wp_unslash( $_POST['classified_category'] ) ) : array();
	if ( empty( $categories ) ) {
		$form_errors[] = 'You must select at least one category.';
	} else {
		foreach ( $categories as $category_id ) {
			if ( ! term_exists( $category_id, 'classified_category' ) ) {
				$form_errors[] = 'Invalid category selected.';
				break;
			}
		}
	}

	$classified_email = isset( $_POST['classified_email'] ) ? sanitize_email( wp_unslash( $_POST['classified_email'] ) ) : '';
	if ( ! is_email( $classified_email ) ) {
		$form_errors[] = 'The provided email address is not valid.';
	}

	if ( ! isset( $_POST['classified_user_type'] ) || empty( $_POST['classified_user_type'] ) ) {
		$form_errors[] = 'You must select if you are a Producer or a Business.';
	}

	if ( ! isset( $_POST['classified_whatsapp'] ) || ! empty( $_POST['classified_whatsapp'] ) ) {
		$classified_whatsapp = sanitize_text_field( wp_unslash( $_POST['classified_whatsapp'] ) );
		if ( ! preg_match( '/^\d{10,15}$/', $classified_whatsapp ) ) {
			$form_errors[] = 'Invalid WhatsApp number format. Please enter the number without spaces or symbols.';
		}
	}

	// Return validation errors if any.
	if ( ! empty( $form_errors ) ) {
		wp_send_json_error( implode( ', ', $form_errors ) );
	}

	// Sanitize and process the classified data if no errors.
	$classified_data = array(
		'post_title'   => sanitize_text_field( wp_unslash( $_POST['classified_title'] ) ),
		'post_content' => isset( $_POST['classified_description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['classified_description'] ) ) : '',
		'post_status'  => 'pending',
		'post_type'    => 'classified',
	);

	// Insert the post.
	$classified_id = wp_insert_post( $classified_data );

	if ( ! $classified_id ) {
		wp_send_json_error( 'There was an error saving the classified.' );
	}

	// Assign the category.
	wp_set_post_terms( $classified_id, $categories, 'classified_category' );

	// Save custom fields.
	update_post_meta( $classified_id, '_classified_price', $classified_price );
	update_post_meta( $classified_id, '_classified_currency', $classified_currency );
	update_post_meta( $classified_id, '_classified_condition', $classified_condition );
	update_post_meta( $classified_id, '_classified_location', sanitize_text_field( wp_unslash( $_POST['classified_location'] ) ) );

	// Save custom fields for Contact info.
	update_post_meta( $classified_id, '_classified_email', $classified_email );
	if ( isset( $classified_whatsapp ) ) {
		update_post_meta( $classified_id, '_classified_whatsapp', $classified_whatsapp );
	}
	update_post_meta( $classified_id, '_classified_user_type', sanitize_text_field( wp_unslash( $_POST['classified_user_type'] ) ) );
	update_post_meta( $classified_id, '_classified_newsletter_subscription', isset( $_POST['newsletter_subscription'] ) ? 1 : 0 );

	// Handle images.
	if ( ! empty( $_FILES['classified_images']['name'][0] ) ) {
		$image_ids   = array();
		$image_count = count( $_FILES['classified_images']['name'] );

		if ( $image_count > 5 ) {
			$image_count = 5;
		}

		$allowed_mime_types = array( 'image/jpeg', 'image/png', 'image/gif', 'image/webp' );
		$max_file_size      = 1 * 1024 * 1024; // 1MB.

		for ( $i = 0; $i < $image_count; $i++ ) {
			$file = array(
				'name'     => isset( $_FILES['classified_images']['name'][ $i ] ) ? sanitize_file_name( wp_unslash( $_FILES['classified_images']['name'][ $i ] ) ) : '',
				'type'     => isset( $_FILES['classified_images']['type'][ $i ] ) ? sanitize_mime_type( wp_unslash( $_FILES['classified_images']['type'][ $i ] ) ) : '',
				'tmp_name' => isset( $_FILES['classified_images']['tmp_name'][ $i ] ) ? sanitize_file_name( wp_unslash( $_FILES['classified_images']['tmp_name'][ $i ] ) ) : '',
				'error'    => isset( $_FILES['classified_images']['error'][ $i ] ) ? intval( wp_unslash( $_FILES['classified_images']['error'][ $i ] ) ) : 0,
				'size'     => isset( $_FILES['classified_images']['size'][ $i ] ) ? intval( wp_unslash( $_FILES['classified_images']['size'][ $i ] ) ) : 0,
			);

			// Validate file type and size.
			if ( ! in_array( $file['type'], $allowed_mime_types, true ) ) {
				wp_send_json_error( 'Invalid file type. Only JPG, PNG, GIF and WEBP are allowed.' );
			}

			if ( $file['size'] > $max_file_size ) {
				wp_send_json_error( 'One or more files exceed the maximum size of 1MB.' );
			}

			if ( UPLOAD_ERR_OK !== $file['error'] ) {
				wp_send_json_error( 'There was an error uploading one or more files.' );
			}

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

	// Send notification email.
	$to = array(
		'comunicpractica@gmail.com',
		'ingfacundomontiel@gmail.com',
		'info@ganaderiaynegocios.com',
	);

	$subject  = 'Nuevo Clasificado - Pendiente de moderación';
	$message  = 'Se ha enviado un nuevo Clasificado.' . "\n\n";
	$message .= 'Título: ' . sanitize_text_field( wp_unslash( $_POST['classified_title'] ) ) . "\n";
	$message .= 'Correo electrónico del vendedor: ' . sanitize_email( wp_unslash( $_POST['classified_email'] ) ) . "\n";
	$message .= 'Para revisar y aprobar el Clasificado, visita el panel de administración de WordPress.' . "\n\n";
	$headers  = array( 'Content-Type: text/plain; charset=UTF-8' );

	wp_mail( $to, $subject, $message, $headers );

	// Respond with success.
	wp_send_json_success();
}

add_action( 'wp_ajax_submit_classified', 'handle_classified_submission' );
add_action( 'wp_ajax_nopriv_submit_classified', 'handle_classified_submission' );
