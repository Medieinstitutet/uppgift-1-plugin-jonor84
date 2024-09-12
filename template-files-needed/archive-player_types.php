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
        <p>Wich player type are you?</p>
    </header>

    <div class="row">
        <?php if ( have_posts() ) : ?>
            <?php while ( have_posts() ) : the_post(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <img src="<?php the_post_thumbnail_url('medium'); ?>" class="card-img-top" alt="<?php the_title(); ?>">
                        <?php endif; ?>
                        <div class="card-body text-center">
                            <h2 class="card-title"><?php the_title(); ?></h2>
                            <p class="card-text"><?php the_excerpt(); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php _e('View Collection', 'j84-playertypes'); ?></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php _e( 'No Player Types found', 'j84-playertypes' ); ?></p>
        <?php endif; ?>
    </div>

    <div class="pagination mt-4">
        <?php the_posts_pagination(); ?>
    </div>
</div>

<?php
get_footer();
