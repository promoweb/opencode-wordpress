<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Opencode_Theme_Example
 */

get_header();
?>

<main id="primary" class="site-main">

    <section class="error-404 not-found">
        <header class="page-header">
            <h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'opencode-theme-example' ); ?></h1>
        </header>

        <div class="page-content">
            <p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'opencode-theme-example' ); ?></p>

            <?php get_search_form(); ?>

            <?php the_widget( 'WP_Widget_Recent_Posts' ); ?>

            <div class="widget-links">
                <h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'opencode-theme-example' ); ?></h2>
                <ul>
                    <?php
                    wp_list_categories( array(
                        'orderby'    => 'count',
                        'order'      => 'DESC',
                        'show_count' => 1,
                        'title_li'   => '',
                        'number'     => 10,
                    ) );
                    ?>
                </ul>
            </div>

            <?php
            wp_list_categories( array(
                'show_option_all' => esc_html__( 'View all categories', 'opencode-theme-example' ),
                'orderby'         => 'count',
                'order'           => 'DESC',
                'show_count'      => 1,
                'title_li'        => '',
                'number'          => 10,
            ) );
            ?>
        </div>
    </section>

</main>

<?php
get_footer();