<?php
$robots = "User-agent: *
Disallow: /wp-admin/
Disallow: /wp-content/uploads/wc-logs/
Disallow: /wp-content/uploads/woocommerce_transient_files/
Disallow: /wp-content/uploads/woocommerce_uploads/
Disallow: /cart/
Disallow: /checkout/
Disallow: /my-account/
Disallow: /wishlist/
Disallow: /*?add-to-cart=
Disallow: /*?add_to_wishlist=
Disallow: /*?remove_item=
Allow: /wp-admin/admin-ajax.php

# Sitemaps
Sitemap: https://suplementospanama.net/sitemap_index.xml
";

update_option('rank_math_robots_txt', $robots);
echo "Robots.txt updated.\n";
