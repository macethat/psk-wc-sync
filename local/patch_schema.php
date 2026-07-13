<?php
/**
 * Parche para agregar validFrom, hasMerchantReturnPolicy, shippingDetails, gtin14
 * al schema de combos en functions.php del child theme.
 */
$file = getenv('HOME') . '/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php';
$code = file_get_contents($file);

// 1. Agregar $created_date despues de $product_cats
$search1 = "\$product_cats = wp_strip_all_tags(wc_get_product_category_list(\$product_id));";
$replace1 = "\$product_cats = wp_strip_all_tags(wc_get_product_category_list(\$product_id));
    \$created_date = get_the_date('c', \$product_id);
    \$gtin_raw = preg_replace('/[^0-9]/', '', \$product_sku ?: 'COMBO-' . \$product_id);
    \$gtin14 = strlen(\$gtin_raw) >= 1 ? str_pad(\$gtin_raw, 14, '0', STR_PAD_LEFT) : '';";
$code = str_replace($search1, $replace1, $code);

// 2. Agregar validFrom, hasMerchantReturnPolicy, shippingDetails dentro de offers
$search2 = "'itemCondition' => 'https://schema.org/NewCondition',";
$replace2 = "'validFrom' => \$created_date,
            'itemCondition' => 'https://schema.org/NewCondition',
            'hasMerchantReturnPolicy' => array(
                '@type' => 'MerchantReturnPolicy',
                'applicableCountry' => 'PA',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays' => 30,
                'returnMethod' => 'https://schema.org/ReturnInStore',
                'returnFees' => 'https://schema.org/FreeReturn',
                'refundType' => 'https://schema.org/FullRefund',
            ),
            'shippingDetails' => array(
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
            ),";
$code = str_replace($search2, $replace2, $code);

// 3. Agregar gtin14 despues de brand
$search3 = "\$schema['brand'] = array(";
$replace3 = "if (\$gtin14) {
        \$schema['gtin14'] = \$gtin14;
    }
    
    \$schema['brand'] = array(";
$code = str_replace($search3, $replace3, $code);

file_put_contents($file, $code);
echo "OK: functions.php parcheado\n";
