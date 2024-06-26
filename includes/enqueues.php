<?php

namespace VinicolaAraucaria;

add_action( 'wp_enqueue_scripts', 'VinicolaAraucaria\\frontend_enqueue_scripts' );

function frontend_enqueue_scripts() {
    wp_enqueue_style( 'vinicola-araucaria', VINICOLA_ARAUCARIA_PATH . 'assets/css/vinicola-araucaria.css', [], VINICOLA_ARAUCARIA_VERSION, 'all' );
    wp_enqueue_script( 'vinicola-araucaria', VINICOLA_ARAUCARIA_PATH . 'assets/js/vinicola-araucaria.js', [], VINICOLA_ARAUCARIA_VERSION, true );

    if ( is_singular( 'product' ) ) {
        wp_localize_script( 'vinicola-araucaria', 'VA_data', [
            'base_url' => esc_url_raw( home_url( 'wp-json' ) ),
            'product_id' => get_the_ID()
        ] );
    }
}
