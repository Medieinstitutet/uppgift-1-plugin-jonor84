<?php
/**
 * Plugin Name: J84 Playertypes
 * Plugin URI: https://github.com/Medieinstitutet/uppgift-1-plugin-jonor84
 * Description: A plugin to allow users to create custom collections (playertypes) of products in WooCommerce that can be purchased by others.
 * Version: 1.0
 * Author: Jonor84
 * Author URI: https://github.com/Medieinstitutet
 * Text Domain: j84-playertypes
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin initialization
function j84_playertypes_init() {
    
}
add_action( 'plugins_loaded', 'j84_playertypes_init' );

