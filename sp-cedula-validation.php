<?php
/**
 * Plugin Name: SP Cedula/Pasaporte/RUC Validation
 * Description: Validacion personalizada de billing_doc_identificacion (acepta formatos panamenos con letras y guiones, ej. E-8-201079, 8-123-456).
 * Version: 1.0
 */

defined('ABSPATH') || exit;

add_action('woocommerce_after_checkout_validation', 'sp_validate_doc_identificacion', 20, 2);
function sp_validate_doc_identificacion($data, $errors) {
    $raw = isset($_POST['billing_doc_identificacion']) ? trim((string) wp_unslash($_POST['billing_doc_identificacion'])) : '';
    if ($raw === '') {
        return; // la obligatoriedad la maneja WooCommerce
    }

    $msg = '';
    if (!preg_match('/^[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*$/', $raw)) {
        $msg = 'El campo <strong>Cédula/Pasaporte/RUC</strong> solo admite letras, números y guiones (sin espacios ni símbolos). Ejemplos válidos: 8-123-456, E-8-201079, PE-8-1234.';
    } elseif (preg_match_all('/[0-9]/', $raw) < 4) {
        $msg = 'El campo <strong>Cédula/Pasaporte/RUC</strong> no parece válido. Debe incluir al menos 4 dígitos (ej: 8-123-456, E-8-201079).';
    } elseif (strlen($raw) < 5 || strlen($raw) > 25) {
        $msg = 'El campo <strong>Cédula/Pasaporte/RUC</strong> debe tener entre 5 y 25 caracteres.';
    }

    if ($msg !== '') {
        $errors->add('sp_doc_identificacion', $msg);
    }
}
