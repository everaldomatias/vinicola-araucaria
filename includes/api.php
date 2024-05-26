<?php

namespace VinicolaAraucaria;

add_action( 'rest_api_init', function () {
    register_rest_route( 'vinicolaaraucaria/v1', '/calculate-price/', [
        'methods' => 'GET',
        'callback' => 'VinicolaAraucaria\calculate_price',
        'args' => [
            'product_id' => [
                'required' => true,
                'validate_callback' => function( $param, $request, $key ) {
                    return is_numeric( $param );
                }
            ],
            'adults' => [
                'required' => true,
                'validate_callback' => function( $param, $request, $key ) {
                    return is_numeric( $param );
                }
            ],
            'children' => [
                'required' => true,
                'validate_callback' => function( $param, $request, $key ) {
                    return is_numeric( $param );
                }
            ]
        ],
        'permission_callback' => '__return_true'
    ] );
} );

function calculate_price( \WP_REST_Request $request ) {
    $product_id = $request['product_id'];
    $adults     = $request['adults'];
    $children   = $request['children'];

    $product = wc_get_product( $product_id );

    if ( ! $product ) {
        return new \WP_Error( 'no_product', 'Invalid product ID', ['status' => 404] );
    }

    $base_price = $product->get_price();
    $adult_price = floatval( get_post_meta( $product_id, '_adult_price', true ) );
    $child_price = floatval( get_post_meta( $product_id, '_child_price', true ) );

    $total_price = $base_price + ( $adult_price * $adults ) + ( $child_price * $children );
    $formatted_price = wc_price( $total_price );

    return new \WP_REST_Response( ['total' => $formatted_price], 200 );
}
