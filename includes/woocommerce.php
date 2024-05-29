<?php

namespace VinicolaAraucaria;

add_filter( 'woocommerce_product_data_tabs', 'VinicolaAraucaria\\add_custom_product_data_tab' );

/**
 * Adiciona abas de configuração da visita guiada
 */
function add_custom_product_data_tab( $tabs ) {
    global $post, $product_object;
    $product = wc_get_product( $post->ID );
    if ( $product && 'variable' === $product->get_type() ) {
        $tabs['tour-settings'] = [
            'label'    => __( 'Visita Guiada', 'araucaria' ),
            'target'   => 'tour_settings_product_data',
            'priority' => 50,
            'class'    => [
                'show_if_tour'
            ]
        ];
        
        \uasort( $tabs, 'VinicolaAraucaria\\order_by_priority' );
    }
    return $tabs;
}

add_action( 'woocommerce_product_data_panels', 'VinicolaAraucaria\\add_custom_product_data_panel' );

/**
 * Adiciona campos de configuração da visita guiada
 */
function add_custom_product_data_panel() {
    ?>
    <div id='tour_settings_product_data' class='panel woocommerce_options_panel'>
        <div class="options_group">
            <?php
                woocommerce_wp_checkbox(
                    [
                        'id'          => '_use_tour',
                        'label'       => __( 'Visita guiada?', 'araucaria' ),
                        'description' => __( 'Marque esta opção para habilitar opções da visita guiada', 'araucaria' ),
                    ]
                );

                woocommerce_wp_text_input(
                    [
                        'id'                => '_adult_price',
                        'label'             => __( 'Valor para cada adulto (R$)', 'araucaria' ),
                        'desc_tip'          => 'true',
                        'description'       => __( 'Adicione o valor incremental para cada adulto da visita.', 'araucaria' ),
                        'type'              => 'number',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min'  => '0'
                        ],
                        'wrapper_class'     => 'show_if_personal_price'
                    ]
                );

                woocommerce_wp_text_input(
                    [
                        'id'                => '_child_price',
                        'label'             => __( 'Valor para cada criança (R$)', 'araucaria' ),
                        'desc_tip'          => 'true',
                        'description'       => __( 'Adicione o valor incremental para cada criança da visita.', 'araucaria' ),
                        'type'              => 'number',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min'  => '0'
                        ],
                        'wrapper_class'     => 'show_if_personal_price'
                    ]
                );

                woocommerce_wp_text_input(
                    [
                        'id'                => '_adult_qty',
                        'label'             => __( 'Quantidade máxima de adultos', 'araucaria' ),
                        'desc_tip'          => 'true',
                        'description'       => __( 'Adicione um limite máximo de adultos para a visita.', 'araucaria' ),
                        'type'              => 'number',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min'  => '1'
                        ],
                        'wrapper_class'     => 'show_if_personal_price'
                    ]
                );

                woocommerce_wp_text_input(
                    [
                        'id'                => '_child_qty',
                        'label'             => __( 'Quantidade máxima de crianças', 'araucaria' ),
                        'desc_tip'          => 'true',
                        'description'       => __( 'Adicione um limite máximo de crianças para a visita.', 'araucaria' ),
                        'type'              => 'number',
                        'custom_attributes' => [
                            'step' => 'any',
                            'min'  => '0'
                        ],
                        'wrapper_class'     => 'show_if_personal_price'
                    ]
                );

                woocommerce_wp_textarea_input(
                    [
                        'id'            => '_terms_tour',
                        'label'         => __( 'Termos de aceite para a visita guiada', 'araucaria' ),
                        'placeholder'   => __( 'Escreva aqui os temos de aceite para a visita guiada', 'araucaria' ),
                        'desc_tip'      => true,
                        'description'   => __( 'Escreva aqui os temos de aceite para a visita guiada', 'araucaria' ),
                        'wrapper_class' => 'show_if_personal_price'
                    ]
                );
            ?>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            function toggle_price_fields() {
                if ($('#_use_tour').is(':checked')) {
                    $('.show_if_personal_price').show();
                } else {
                    $('.show_if_personal_price').hide();
                }
            }
            toggle_price_fields();
            $('#_use_tour').change(toggle_price_fields);
        });
    </script>
    <?php
}



add_action( 'woocommerce_process_product_meta', 'VinicolaAraucaria\\save_tour_settings_fields', 10, 2 );

/**
 * Save custom tab to product data
 */
function save_tour_settings_fields( $post_id ) {
    $use_personal_price = isset( $_POST['_use_tour'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_use_tour', $use_personal_price );

    if ( $use_personal_price === 'yes' ) {
        if ( isset( $_POST['_adult_price'] ) ) {
            update_post_meta( $post_id, '_adult_price', sanitize_text_field( $_POST['_adult_price'] ) );
        }
        if ( isset( $_POST['_child_price'] ) ) {
            update_post_meta( $post_id, '_child_price', sanitize_text_field( $_POST['_child_price'] ) );
        }
        if ( isset( $_POST['_adult_qty'] ) ) {
            update_post_meta( $post_id, '_adult_qty', sanitize_text_field( $_POST['_adult_qty'] ) );
        }
        if ( isset( $_POST['_child_qty'] ) ) {
            update_post_meta( $post_id, '_child_qty', sanitize_text_field( $_POST['_child_qty'] ) );
        }
        if ( isset( $_POST['_terms_tour'] ) ) {
            update_post_meta( $post_id, '_terms_tour', sanitize_textarea_field( $_POST['_terms_tour'] ) );
        }
    }
}


/**
 * Função para orderar por prioridade
 */
function order_by_priority( $a, $b ) {
    $a = $a['priority'] ?? 0;
    $b = $b['priority'] ?? 0;

	return $a - $b;
}


add_action( 'woocommerce_before_mini_cart_contents',  'VinicolaAraucaria\\force_recalculate_wc_totals' );

/**
 * Força o recálculo dos valores totais do carrinho
 */
function force_recalculate_wc_totals() {
    if ( wp_doing_ajax() && ! empty( $_GET['wc-ajax'] ) && $_GET['wc-ajax'] === 'get_refreshed_fragments' ) {
        WC()->cart->calculate_totals();
        WC()->cart->set_session();
        WC()->cart->maybe_set_cart_cookies();
    }
}


add_action( 'woocommerce_before_add_to_cart_button', 'VinicolaAraucaria\\add_terms_and_conditions_checkbox' );

/**
 * Adiciona checkbox de aceite de termos e condições ao formulário de adicionar ao carrinho
 */
function add_terms_and_conditions_checkbox() {
    $product_id = get_the_ID();
    $product    = wc_get_product( $product_id );

    $_use_tour = get_post_meta( $product_id, '_use_tour', true );
    $_tour_terms = get_post_meta( $product_id, '_terms_tour', true );

    if ( $product && 'variable' === $product->get_type() && $_use_tour === 'yes' ) {
    ?>
        <div class="terms-and-conditions">
            <?= apply_filters( 'the_content', $_tour_terms ) ?>
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="terms_and_conditions" id="terms_and_conditions">
                <span class="required">*</span><span>Li e aceito os termos</span>
            </label>
        </div>
    <?php
    }
}


add_action( 'wp_footer', 'VinicolaAraucaria\\add_custom_js_to_footer' );

/**
 * Adiciona script ao rodapé para habilitar/desabilitar o botão de adicionar ao carrinho
 */
function add_custom_js_to_footer() {
    $product_id = get_the_ID();
    $product    = wc_get_product( $product_id );

    if ( $product && 'variable' === $product->get_type() ) {
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.single_add_to_cart_button').prop('disabled', true);

                $('#terms_and_conditions').change(function() {
                    if ($(this).is(':checked')) {
                        $('.single_add_to_cart_button').prop('disabled', false);
                    } else {
                        $('.single_add_to_cart_button').prop('disabled', true);
                    }
                });
            });
        </script>
    <?php
    }
}


add_filter( 'body_class', 'VinicolaAraucaria\\add_body_class' );

/**
 * Adiciona classe "use-tour" ao body quando o produto é da tipo "tour"
 */
function add_body_class( $classes ) {
    if ( is_singular( 'product' ) ) {
        global $post;
        $_use_tour = get_post_meta( $post->ID, '_use_tour', true );
        if ( 'yes' === $_use_tour ) {
            $classes[] = 'use-tour';
        }
    }
    return $classes;
}

add_filter( 'woocommerce_product_needs_shipping', 'VinicolaAraucaria\\check_product_needs_shipping', 10, 2 );

/**
 * Verifica se um produto precisa de envio.
 *
 * Esta função é um filtro para o hook 'woocommerce_product_needs_shipping' do WooCommerce.
 * Ela verifica se o produto é do tipo 'variável' e se a meta-chave '_use_tour' está definida como 'yes'.
 * Se essas condições forem atendidas, a função define que o produto não precisa de envio.
 *
 * @param bool $needs_shipping Indica se o produto precisa de envio ou não.
 * @param WC_Product $product O objeto do produto.
 *
 * @return bool Retorna true se o produto precisa de envio, false caso contrário.
 */
function check_product_needs_shipping ( $needs_shipping, $product ) {
    if ( $product->is_type( 'variable' ) ) {
        $_use_tour = get_post_meta( $post->ID, '_use_tour', true );

        if ( 'yes' === $_use_tour ) {
            $needs_shipping = false;
        }
    }
    return $needs_shipping;
}