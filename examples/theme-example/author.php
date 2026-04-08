<?php
/**
 * The template for displaying author archive pages
 *
 * @package Opencode_Theme_Example
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php if (have_posts()) : ?>

        <header class="page-header author-header">
            <?php
            // Get author data
            $author_id = get_queried_object_id();
            $author_name = get_the_author_meta('display_name', $author_id);
            $author_description = get_the_author_meta('description', $author_id);
            $author_avatar = get_avatar($author_id, 120);
            ?>
            
            <div class="author-info">
                <div class="author-avatar">
                    <?php echo $author_avatar; ?>
                </div>
                
                <div class="author-details">
                    <h1 class="page-title author-title">
                        <?php 
                        printf(
                            /* translators: %s: Author name. */
                            esc_html__('Author: %s', 'opencode-theme-example'),
                            esc_html($author_name)
                        ); 
                        ?>
                    </h1>
                    
                    <?php if ($author_description) : ?>
                        <div class="author-description">
                            <?php echo wp_kses_post(wpautop($author_description)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <?php
        /* Start the Loop */
        while (have_posts()) :
            the_post();

            get_template_part('template-parts/content', get_post_type());

        endwhile;

        the_posts_navigation();

    else :

        get_template_part('template-parts/content', 'none');

    endif;
    ?>
</main>

<?php
get_sidebar();
get_footer();
