<?php
/**
 * Functions used by plugins
 */

function my_custom_endpoints() {
    add_rewrite_endpoint( 'special-page', EP_ROOT | EP_PAGES );
}

add_action( 'init', 'my_custom_endpoints' );

function my_custom_query_vars( $vars ) {
    $vars[] = 'special-page';

    return $vars;
}

add_filter( 'query_vars', 'my_custom_query_vars', 0 );

function my_custom_flush_rewrite_rules() {
    flush_rewrite_rules();
}

add_action( 'wp_loaded', 'my_custom_flush_rewrite_rules' );
