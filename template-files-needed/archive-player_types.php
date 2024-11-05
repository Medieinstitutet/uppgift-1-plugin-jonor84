<?php

/**
 * This is a part of plugin: J84 Playertypes
 * Plugin URI: https://github.com/Medieinstitutet/uppgift-1-plugin-jonor84
 */

get_header();
?>

<div class="container py-5">

    <header class="text-center mb-5">
        <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
        <p><?php _e('Which player type are you?', 'j84-playertypes'); ?></p>

        <form id="filter-form" method="GET" action="">
            <label for="filter-date"><?php _e('Filter by Date:', 'j84-playertypes'); ?></label>
            <input type="month" name="filter_date" id="filter-date" value="<?php echo isset($_GET['filter_date']) ? esc_attr($_GET['filter_date']) : ''; ?>" />

            <label for="filter-category"><?php _e('Filter by Category:', 'j84-playertypes'); ?></label>
            <select name="filter_category" id="filter-category">
                <option value=""><?php _e('All Categories', 'j84-playertypes'); ?></option>
                <?php
                $categories = get_terms(array(
                    'taxonomy' => 'product_cat',
                    'hide_empty' => false,
                ));
                foreach ($categories as $category) {
                    echo '<option value="' . esc_attr($category->slug) . '" ' . selected($_GET['filter_category'], $category->slug, false) . '>' . esc_html($category->name) . '</option>';
                }
                ?>
            </select>
            <button type="submit"><?php _e('Apply Filters', 'j84-playertypes'); ?></button>
        </form>
    </header>

    <div class="row">
        <?php
        $args = array(
            'post_type' => 'player_types',
            'posts_per_page' => -1,
        );

        if (isset($_GET['filter_date']) && !empty($_GET['filter_date'])) {
            $date = explode('-', $_GET['filter_date']);
            $args['date_query'] = array(
                array(
                    'year'  => $date[0],
                    'month' => $date[1],
                ),
            );
        }

        $player_types = new WP_Query($args);

        if ($player_types->have_posts()) :
            while ($player_types->have_posts()) : $player_types->the_post();

                $products = get_post_meta(get_the_ID(), 'j84_collection_products', true);

                if (! empty($products)) {
                    $product_ids = explode(',', $products);
                    $found_product_in_category = false;

                    foreach ($product_ids as $product_id) {
                        $terms = wp_get_post_terms($product_id, 'product_cat');

                        if (! empty($_GET['filter_category'])) {
                            foreach ($terms as $term) {
                                if ($term->slug == $_GET['filter_category']) {
                                    $found_product_in_category = true;
                                    break;
                                }
                            }
                        } else {
                            $found_product_in_category = true;
                        }
                    }

                    if ($found_product_in_category) : ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php if (has_post_thumbnail()) : ?>
                                    <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title(); ?>">
                                <?php endif; ?>
                                <div class="card-body text-center">
                                    <h2 class="card-title"><?php the_title(); ?></h2>
                                    <p class="card-text"><?php the_excerpt(); ?></p>
                                    <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php _e('View Collection', 'j84-playertypes'); ?></a>
                                </div>
                            </div>
                        </div>
            <?php endif;
                }

            endwhile;
        else : ?>
            <p><?php _e('No Player Types found', 'j84-playertypes'); ?></p>
        <?php endif;
        wp_reset_postdata(); ?>
    </div>


    <div class="pagination mt-4">
        <?php the_posts_pagination(); ?>
    </div>
</div>

<?php
get_footer();
