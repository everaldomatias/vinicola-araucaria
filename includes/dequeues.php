<?php

namespace VinicolaAraucaria;

function frontend_dequeue_scripts() {
    if ( ! is_admin() ) {
        // Remove scripts from plugin Advanced Product Fields Extended for WooCommerce
        wp_dequeue_script( 'wapf-extended' );
        wp_dequeue_script( 'wapf-frontend' );
    }
}

\add_action( 'wp_enqueue_scripts', 'VinicolaAraucaria\\frontend_dequeue_scripts', 6000 );
