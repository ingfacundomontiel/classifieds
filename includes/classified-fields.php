<?php
/**
 * Defines custom fields for the Classified CPT.
 *
 * This file centralizes the custom field keys used throughout the Classifieds plugin.
 *
 * @package classifieds
 */

defined( 'ABSPATH' ) || exit;

// Array holding custom field keys for the Classified CPT.
$classified_custom_fields = array(
    'price'      => '_classified_price',      // Price of the classified item.
    'currency'   => '_classified_currency',   // Currency for the price (e.g., ARS, USD).
    'email'      => '_classified_email',      // Contact email of the classified owner.
    'whatsapp'   => '_classified_whatsapp',   // WhatsApp number for contact.
    'user_type'  => '_classified_user_type',  // User type, either "Productor" or "Comercio".
    'condition'  => '_classified_condition',  // Condition of the item, either "Nuevo" or "Usado".
);
