<?php

namespace PluginTemplate;

add_action( 'wp_enqueue_scripts', 'PluginTemplate\\frontend_enqueue_scripts' );

function frontend_enqueue_scripts() {
    wp_enqueue_style( 'plugin-template', PLUGIN_TEMPLATE_PATH . 'assets/css/plugin-template.css', [], PLUGIN_TEMPLATE_VERSION, 'all' );
    wp_enqueue_script( 'plugin-template', PLUGIN_TEMPLATE_PATH . 'assets/js/plugin-template.js', [], PLUGIN_TEMPLATE_VERSION, true );
}
