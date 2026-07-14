<?php
/**
 * Plugin Name: SP Global Product Schema — Return Policy, Shipping, GTIN
 * Description: Agrega hasMerchantReturnPolicy, shippingDetails, validFrom y GTIN
 *              a TODOS los productos via filtro rank_math/json_ld.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

add_filter('rank_math/json_ld', function(array $data, $jsonld) {
    if (!is_singular('product')) {
        return $data;
    }

    if (!isset($data['@graph']) || !is_array($data['@graph'])) {
        return $data;
    }

    $product = wc_get_product(get_queried_object_id());
    if (!$product) {
        return $data;
    }

    $created_date = get_the_date('c', $product->get_id());

    foreach ($data['@graph'] as &$node) {
        if (!is_array($node) || !isset($node['@type'])) {
            continue;
        }

        $types = (array) $node['@type'];

        // --- Organization level: global policies ---
        if (in_array('Organization', $types, true) || in_array('Store', $types, true)) {
            $node['hasMerchantReturnPolicy'] = array(
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'PA',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays' => 30,
                'returnMethod' => 'https://schema.org/ReturnInStore',
                'returnFees' => 'https://schema.org/FreeReturn',
                'refundType' => 'https://schema.org/FullRefund',
            );
            continue;
        }

        // --- Product level ---
        if (!in_array('Product', $types, true)) {
            continue;
        }

        // GTIN desde SKU
        $sku = $product->get_sku();
        if ($sku) {
            $gtin_raw = preg_replace('/[^0-9]/', '', $sku);
            if (strlen($gtin_raw) >= 1) {
                $node['gtin14'] = str_pad($gtin_raw, 14, '0', STR_PAD_LEFT);
            }
        }

        // Offers node
        if (isset($node['offers']) && is_array($node['offers'])) {
            $is_single = isset($node['offers']['@type']);
            $offer = &$node['offers'];

            if ($is_single) {
                $offer['validFrom'] = $created_date;
                $offer['hasMerchantReturnPolicy'] = array(
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'PA',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 30,
                    'returnMethod' => 'https://schema.org/ReturnInStore',
                    'returnFees' => 'https://schema.org/FreeReturn',
                    'refundType' => 'https://schema.org/FullRefund',
                );
                $offer['shippingDetails'] = array(
                    '@type' => 'OfferShippingDetails',
                    'shippingRate' => array(
                        '@type' => 'MonetaryAmount',
                        'value' => 0,
                        'currency' => 'USD',
                    ),
                    'shippingDestination' => array(
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'PA',
                    ),
                    'deliveryTime' => array(
                        '@type' => 'ShippingDeliveryTime',
                        'handlingTime' => array(
                            '@type' => 'QuantitativeValue',
                            'minValue' => 0,
                            'maxValue' => 1,
                            'unitCode' => 'DAY',
                        ),
                        'transitTime' => array(
                            '@type' => 'QuantitativeValue',
                            'minValue' => 1,
                            'maxValue' => 5,
                            'unitCode' => 'DAY',
                        ),
                    ),
                );
            }
        }

        break;
    }

    return $data;
}, 20, 2);
