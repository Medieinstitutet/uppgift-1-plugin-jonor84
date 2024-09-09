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

// Register custom post type for Playertypes
function j84_register_playertypes_cpt() {
    error_log('Custom Post Type Player Types is being registered');
    $labels = array(
        'name'               => __( 'Player Types', 'j84-playertypes' ),
        'singular_name'      => __( 'Player Type', 'j84-playertypes' ),
        'menu_name'          => __( 'Player Types', 'j84-playertypes' ),
        'add_new'            => __( 'Add New', 'j84-playertypes' ),
        'add_new_item'       => __( 'Add New Player Type', 'j84-playertypes' ),
        'edit_item'          => __( 'Edit Player Type', 'j84-playertypes' ),
        'new_item'           => __( 'New Player Type', 'j84-playertypes' ),
        'all_items'          => __( 'All Player Types', 'j84-playertypes' ),
        'view_item'          => __( 'View Player Type', 'j84-playertypes' ),
        'search_items'       => __( 'Search Player Types', 'j84-playertypes' ),
        'not_found'          => __( 'No Player Types found.', 'j84-playertypes' ),
        'not_found_in_trash' => __( 'No Player Types found in Trash.', 'j84-playertypes' )
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable'  => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array( 'slug' => 'playertypes' ),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 20, 
        'menu_icon'          => 'dashicons-groups', 
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true, 
        'show_in_admin_bar'  => true,
    );

    register_post_type( 'player_types', $args );
}
add_action( 'init', 'j84_register_playertypes_cpt' );

// Add collection to cart
function j84_add_collection_to_cart() {
    if ( isset($_GET['add_collection_to_cart']) ) {
        $collection_id = intval($_GET['add_collection_to_cart']);
        $products = get_post_meta($collection_id, 'j84_collection_products', true);

        if ( $products ) {
            $product_ids = explode( ',', $products );
            foreach ( $product_ids as $product_id ) {
                $product_id = intval($product_id);
                $product = wc_get_product( $product_id );
                if ( $product && $product->is_in_stock() && $product->is_purchasable() ) {
                    WC()->cart->add_to_cart( $product_id );
                }
            }
            wp_safe_redirect( wc_get_cart_url() );
            exit;
        }
    }
}
add_action( 'wp', 'j84_add_collection_to_cart' );

// Create and save metabox for product-ID
function j84_add_player_type_metabox() {
    add_meta_box(
        'j84_player_type_products',
        __( 'Add Products to Collection', 'j84-playertypes' ),
        'j84_player_type_metabox_callback',
        'player_types',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'j84_add_player_type_metabox' );

function j84_player_type_metabox_callback( $post ) {
    $products = get_post_meta( $post->ID, 'j84_collection_products', true );
    wp_nonce_field( 'j84_save_player_type_nonce', 'j84_player_type_nonce' );
    ?>
    <label for="j84_collection_products"><?php _e( 'Product IDs (comma separated)', 'j84-playertypes' ); ?></label>
    <input type="text" name="j84_collection_products" value="<?php echo esc_attr( $products ); ?>" size="50" />
    <?php
}

function j84_save_player_type( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! isset( $_POST['j84_player_type_nonce'] ) || ! wp_verify_nonce( $_POST['j84_player_type_nonce'], 'j84_save_player_type_nonce' ) ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['j84_collection_products'] ) ) {
        $products = sanitize_text_field( $_POST['j84_collection_products'] );
        if ( empty( $products ) ) {
            delete_post_meta( $post_id, 'j84_collection_products' );
        } else {
            $product_ids = explode( ',', $products );
            $valid_products = array();
            foreach ( $product_ids as $product_id ) {
                $product_id = trim( $product_id );
                if ( is_numeric( $product_id ) && get_post_type( $product_id ) == 'product' ) {
                    $valid_products[] = $product_id;
                }
            }
            if ( ! empty( $valid_products ) ) {
                $valid_products_string = implode( ',', $valid_products );
                update_post_meta( $post_id, 'j84_collection_products', $valid_products_string );
            } else {
                delete_post_meta( $post_id, 'j84_collection_products' );
            }
        }
    }
}
add_action( 'save_post', 'j84_save_player_type' );
