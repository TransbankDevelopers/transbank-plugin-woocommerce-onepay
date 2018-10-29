composer install
wp core install --url=localhost:8080 --title=Onepay --admin_user=onepay --admin_password=onepay --admin_email=transbankdevelopers@continuum.cl
wp plugin install woocommerce
wp plugin activate woocommerce
wp plugin activate onepay

wp wc tool run install_pages --user=onepay
wp wc product create --name="Zapatos deportivos" --sku=1 --regular_price=1000 --status=publish --user=onepay
wp theme install storefront
wp theme activate storefront
wp wc payment_gateway update onepay --enabled=true  --user=onepay
wp db query "UPDATE wp_options SET option_value='CLP' WHERE option_name='woocommerce_currency';"
wp db query "UPDATE wp_options SET option_value='General Bustamante 24' WHERE option_name='woocommerce_store_address';"
wp db query "UPDATE wp_options SET option_value='Of M, Piso 7' WHERE option_name='woocommerce_store_address_2';"
wp db query "UPDATE wp_options SET option_value='Providencia' WHERE option_name='woocommerce_store_city';"
wp db query "UPDATE wp_options SET option_value='CL' WHERE option_name='woocommerce_default_country';"
wp db query "UPDATE wp_options SET option_value='7500000' WHERE option_name='woocommerce_store_postcode';"

wp db query "UPDATE wp_options SET option_value=0 WHERE option_name='woocommerce_price_num_decimals';"
wp db query "UPDATE wp_options SET option_value='.' WHERE option_name='woocommerce_price_thousand_sep';"
wp db query "UPDATE wp_options SET option_value=',' WHERE option_name='woocommerce_price_decimal_sep';"

wp config set WP_DEBUG true
wp config set --add --type=constant WP_DEBUG_LOG true
wp config set --add --type=constant WP_DEBUG_DISPLAY false
wp config set --add --type=constant WPS_DEBUG true
wp config set --add --type=constant WPS_DEBUG_SCRIPTS true
wp config set --add --type=constant WPS_DEBUG_STYLES true
