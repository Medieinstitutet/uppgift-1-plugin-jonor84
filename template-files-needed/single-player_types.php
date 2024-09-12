<?php
/**
 * This is a part of plugin: J84 Playertypes
 * Plugin URI: https://github.com/Medieinstitutet/uppgift-1-plugin-jonor84
 */

get_header();

if ( ! class_exists( 'WooCommerce' ) ) {
    echo '<p>' . __( 'WooCommerce is not installed or activated.', 'j84-playertypes' ) . '</p>';
    return;
}

$products = get_post_meta( get_the_ID(), 'j84_collection_products', true );

echo '<div class="container py-5">';

if ( ! empty( $products ) ) {
    $product_ids = explode( ',', $products );

    if ( ! empty( $product_ids ) ) {
        echo '<header class="text-center mb-5">';
        echo '<h1>' . __( 'Products in Collection: ', 'j84-playertypes' ) . get_the_title() . '</h1>';
        echo '</header>';

        echo '<div class="row">';
        foreach ( $product_ids as $product_id ) {
            $product = wc_get_product( $product_id );

            if ( $product ) {
                echo '<div class="col-md-4 mb-4">';
                echo '<div class="card h-100">';
                
                if ( has_post_thumbnail( $product_id ) ) {
                    echo '<img src="' . get_the_post_thumbnail_url( $product_id, 'medium' ) . '" class="card-img-top" alt="' . $product->get_name() . '">';
                }

                echo '<div class="card-body">';
                echo '<h4 class="card-title">' . $product->get_name() . '</h4>';
                echo '<p class="card-text">' . $product->get_price_html() . '</p>';
                echo '<a href="' . get_permalink( $product_id ) . '" class="btn btn-primary">' . __( 'View Product', 'j84-playertypes' ) . '</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        }
        echo '</div>';

        echo '<div class="text-center mt-4">';
        echo '<a href="?add_collection_to_cart=' . get_the_ID() . '" class="btn btn-success">';
        echo __( 'Add All to Cart', 'j84-playertypes' );
        echo '</a>';
        echo '</div>';
    } else {
        echo '<p class="text-center">' . __( 'No valid products found in this collection.', 'j84-playertypes' ) . '</p>';
    }

} else {
    echo '<p class="text-center">' . __( 'No products found in this collection.', 'j84-playertypes' ) . '</p>';
}

echo '</div>'; 

get_footer();
