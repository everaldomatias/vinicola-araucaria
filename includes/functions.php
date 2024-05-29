<?php

namespace VinicolaAraucaria;

add_action( 'woocommerce_before_add_to_cart_button', 'VinicolaAraucaria\\add_custom_field_before_add_to_cart_button' );

function add_custom_field_before_add_to_cart_button() {
    $product_id = get_the_ID();
    $product = wc_get_product( $product_id );
    $_use_tour = get_post_meta( $product->get_id(), '_use_tour', true );

    if ( $product && 'variable' === $product->get_type() && $_use_tour === 'yes' ) {
        $_adult_price = ( $_adult_price = get_post_meta( $product_id, '_adult_price', true ) ) ? $_adult_price : 0;
        $_child_price = ( $_child_price = get_post_meta( $product_id, '_child_price', true ) ) ? $_child_price : 0;
        $_adult_qty   = ( $_adult_qty = get_post_meta( $product_id, '_adult_qty', true ) ) ? $_adult_qty : 1;
        $_child_qty   = ( $_child_qty = get_post_meta( $product_id, '_child_qty', true ) ) ? $_child_qty : 0;

        if ( $_adult_price ) {
            echo '<div class="custom-field-wrap adult-price">';
            echo '<label for="adult_price">Quantidade de adultos</label>';
            echo '<select id="adult_price" name="adult_price">';
            for ( $i = 1; $i <= $_adult_qty; $i++ ) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }

        if ( $_child_price && $_child_qty >= 1 ) {
            echo '<div class="custom-field-wrap child-price">';
            echo '<label for="child_price">Quantidade de crianças</label>';
            echo '<select id="child_price" name="child_price">';
            for ( $i = 0; $i <= $_child_qty; $i++ ) {
                echo '<option value="' . $i . '">' . $i . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }

        echo '<div class="custom-field-wrap recalculate-total-price">';
            echo $product->get_price_html();
        echo '</div>';

    }
}

add_filter( 'woocommerce_add_cart_item_data', 'VinicolaAraucaria\\add_custom_option_item_data', 10, 2 );

function add_custom_option_item_data( $cart_item_data, $product_id ) {
    $_use_tour = get_post_meta( $product_id, '_use_tour', true );

    if ( $_use_tour === 'yes' ) {
        if ( isset( $_POST['adult_price'] ) ) {
            $cart_item_data['adult_price'] = sanitize_text_field( $_POST['adult_price'] );
            $cart_item_data['unique_key'] = md5( microtime().rand() );
        }
    
        if ( isset( $_POST['child_price'] ) ) {
            $cart_item_data['child_price'] = sanitize_text_field( $_POST['child_price'] );
            $cart_item_data['unique_key'] = md5( microtime().rand() );
        }
    }

    return $cart_item_data;
}

add_action( 'woocommerce_before_calculate_totals', 'VinicolaAraucaria\\add_custom_price', 9999, 1 );

function add_custom_price( $cart ) {
    // if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    foreach ( $cart->get_cart() as $cart_item ) {
        $additional_adult_price = 0;
        $additional_child_price = 0;
        
        // Adults
        if ( isset( $cart_item['adult_price'] ) ) {
            $_adult_price = ( $_adult_price = get_post_meta( $cart_item['product_id'], '_adult_price', true ) ) ? $_adult_price : 0;
            $additional_adult_price = $_adult_price * $cart_item['adult_price'];
        }

        // Children
        if ( isset( $cart_item['child_price'] ) ) {
            $_child_price = ( $_child_price = get_post_meta( $cart_item['product_id'], '_child_price', true ) ) ? $_child_price : 0;
            $additional_child_price = $_child_price * $cart_item['child_price'];
        }

        $additional_price = $additional_adult_price + $additional_child_price;
        $cart_item['data']->set_price( ( $cart_item['data']->get_price() + $additional_price ) );
    }
}

function display_tour_data_in_cart( $item_data, $cart_item ) {
    if ( array_key_exists( 'adult_price', $cart_item ) ) {
        $item_data[] = array(
            'key'   => __( 'Qtd. de adultos', 'araucaria' ),
            'value' => wc_clean( $cart_item['adult_price'] )
        );
    }

    if ( array_key_exists( 'child_price', $cart_item ) ) {
        $item_data[] = array(
            'key'   => __( 'Qtd de crianças', 'araucaria' ),
            'value' => wc_clean( $cart_item['child_price'] )
        );
    }

    return $item_data;
}

add_filter( 'woocommerce_get_item_data', 'VinicolaAraucaria\\display_tour_data_in_cart', 10, 2 );

function save_tour_data_to_order_items( $item, $cart_item_key, $values, $order ) {
    $_use_tour = get_post_meta( $item->get_product_id(), '_use_tour', true );

    if ( $_use_tour === 'yes' ) {
        if ( array_key_exists( 'adult_price', $values ) ) {
            $item->add_meta_data( __( 'Qtd. de Adultos', 'araucaria' ), $values['adult_price'] );
        }
    
        if ( array_key_exists( 'child_price', $values ) ) {
            $item->add_meta_data( __( 'Qtd. de Crianças', 'araucaria' ), $values['child_price'] );
        }
    }
}

add_action( 'woocommerce_checkout_create_order_line_item', 'VinicolaAraucaria\\save_tour_data_to_order_items', 10, 4 );