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
	// Verify that the request is an AJAX call.
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {
		wp_send_json_error( 'Invalid request.' );
	}

	// Check if it's a valid AJAX request.
	if ( ! isset( $_POST['action'] ) || 'submit_classified' !== $_POST['action'] ) {
		wp_send_json_error( 'Invalid submission method.' );
	}

	// Check if the nonce is set and valid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'submit_classified' ) ) {
		wp_send_json_error( 'Security error: form could not be processed.' );
	}

	// Validate required fields.
	if ( empty( $_POST['classified_title'] ) ) {
		$form_errors[] = 'The title is required.';
	}
	if ( empty( $_POST['classified_price'] ) ) {
		$form_errors[] = 'The price is required.';
	}
	if ( empty( $_POST['classified_currency'] ) ) {
		$form_errors[] = 'You must select a currency.';
	}
	if ( empty( $_POST['classified_condition'] ) ) {
		$form_errors[] = 'You must select a condition for the product.';
	}
	if ( empty( $_POST['classified_location'] ) ) {
		$form_errors[] = 'The location is required.';
	}
	if ( empty( $_POST['classified_category'] ) ) {
		$form_errors[] = 'You must select at least one category.';
	}
	if ( empty( $_POST['classified_email'] ) ) {
		$form_errors[] = 'The email is required.';
	}
	if ( empty( $_POST['classified_user_type'] ) ) {
		$form_errors[] = 'You must select if you are a Producer or a Business.';
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

	// Variables for Classified info.
	$classified_price     = floatval( wp_unslash( $_POST['classified_price'] ) );
	$classified_currency  = sanitize_text_field( wp_unslash( $_POST['classified_currency'] ) );
	$classified_condition = sanitize_text_field( wp_unslash( $_POST['classified_condition'] ) );
	$classified_location  = sanitize_text_field( wp_unslash( $_POST['classified_location'] ) );

	// Variables for Contact info.
	$classified_email                   = sanitize_email( wp_unslash( $_POST['classified_email'] ) );
	$classified_whatsapp                = isset( $_POST['classified_whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['classified_whatsapp'] ) ) : '';
	$classified_user_type               = sanitize_text_field( wp_unslash( $_POST['classified_user_type'] ) );
	$classified_newsletter_subscription = isset( $_POST['newsletter_subscription'] ) ? 1 : 0;

	// Insert the post.
	$classified_id = wp_insert_post( $classified_data );

	if ( ! $classified_id ) {
		wp_send_json_error( 'There was an error saving the classified.' );
	}

	// Assign the category.
	$categories = array_map( 'intval', wp_unslash( $_POST['classified_category'] ) );
	wp_set_post_terms( $classified_id, $categories, 'classified_category' );

	// Save custom fields.
	update_post_meta( $classified_id, '_classified_price', $classified_price );
	update_post_meta( $classified_id, '_classified_currency', $classified_currency );
	update_post_meta( $classified_id, '_classified_condition', $classified_condition );
	update_post_meta( $classified_id, '_classified_location', $classified_location );

	// Save custom fields for Contact info.
	update_post_meta( $classified_id, '_classified_email', $classified_email );
	update_post_meta( $classified_id, '_classified_whatsapp', $classified_whatsapp );
	update_post_meta( $classified_id, '_classified_user_type', $classified_user_type );
	update_post_meta( $classified_id, '_classified_newsletter_subscription', $classified_newsletter_subscription );

	// Handle images.
	if ( ! empty( $_FILES['classified_images']['name'][0] ) ) {
		$image_ids   = array();
		$image_count = count( $_FILES['classified_images']['name'] );

		if ( $image_count > 5 ) {
			$image_count = 5;
		}

		for ( $i = 0; $i < $image_count; $i++ ) {
			$file = array(
				'name'     => isset( $_FILES['classified_images']['name'][ $i ] ) ? sanitize_file_name( wp_unslash( $_FILES['classified_images']['name'][ $i ] ) ) : '',
				'type'     => isset( $_FILES['classified_images']['type'][ $i ] ) ? sanitize_mime_type( wp_unslash( $_FILES['classified_images']['type'][ $i ] ) ) : '',
				'tmp_name' => isset( $_FILES['classified_images']['tmp_name'][ $i ] ) ? sanitize_file_name( wp_unslash( $_FILES['classified_images']['tmp_name'][ $i ] ) ) : '',
				'error'    => isset( $_FILES['classified_images']['error'][ $i ] ) ? intval( wp_unslash( $_FILES['classified_images']['error'][ $i ] ) ) : 0,
				'size'     => isset( $_FILES['classified_images']['size'][ $i ] ) ? intval( wp_unslash( $_FILES['classified_images']['size'][ $i ] ) ) : 0,
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

	// Send notification email.
	$to       = array(
		'comunicpractica@gmail.com',
		'ingfacundomontiel@gmail.com',
		'info@ganaderiaynegocios.com',
	);
	$subject  = 'New Classified - Pending Moderation';
	$message  = "A new classified has been submitted.\n\n";
	$message .= 'Title: ' . $classified_data['post_title'] . "\n";
	$message .= 'Seller Email: ' . $classified_email . "\n\n";
	$message .= 'To review and approve the classified, visit the WordPress admin panel.' . "\n\n";
	$headers  = array( 'Content-Type: text/plain; charset=UTF-8' );

	wp_mail( $to, $subject, $message, $headers );

	// Respond with success.
	wp_send_json_success();
}

add_action( 'wp_ajax_submit_classified', 'handle_classified_submission' );
add_action( 'wp_ajax_nopriv_submit_classified', 'handle_classified_submission' );
