<?php
/**
 * Plugin Name:       Vinícola Araucária por EveraldoDev
 * Plugin URI:        https://everaldo.dev/plugins/
 * Description:       Plugin to custom and improvements WooCommerce.
 * Version:           0.0.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Everaldo Matias
 * Author URI:        https://everaldo.dev/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://everaldo.dev/plugins/vinicola-araucaria
 * Text Domain:       vinicola-araucaria
 * Domain Path:       /languages
 */

function vinicola_araucaria_plugin_activate() {
    flush_rewrite_rules();
}

register_activation_hook( __FILE__, 'vinicola_araucaria_plugin_activate' );

define( 'VINICOLA_ARAUCARIA_VERSION', '0.0.2' );
define( 'VINICOLA_ARAUCARIA_PATH', plugins_url( '/', __FILE__ ) );

require_once( 'includes/enqueues.php' );
require_once( 'includes/api.php' );
require_once( 'includes/functions.php' );
require_once( 'includes/woocommerce.php' );