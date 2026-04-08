<?php
/**
 * The footer for the theme
 *
 * @package Opencode_Theme_Example
 */

?>

<?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3')) : ?>
    <div class="footer-widgets">
        <?php if (is_active_sidebar('footer-1')) : ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-2')) : ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer-2'); ?>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-3')) : ?>
            <div class="footer-widget-area">
                <?php dynamic_sidebar('footer-3'); ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<footer id="colophon" class="site-footer">
    <div class="site-info">
        <div class="footer-navigation">
            <?php
            if (has_nav_menu('footer')) {
                wp_nav_menu(array(
                    'theme_location' => 'footer',
                    'menu_class'     => 'footer-menu',
                    'depth'          => 1,
                ));
            }
            ?>
        </div>

        <div class="copyright">
            <?php
            printf(
                /* translators: 1: Theme name, 2: Theme author. */
                esc_html__('Theme: %1$s by %2$s.', 'opencode-theme-example'),
                '<a href="https://example.com/themes/opencode-theme-example/">OpenCode Theme Example</a>',
                '<a href="https://example.com">OpenCode</a>'
            );
            ?>
            <span class="sep"> | </span>
            <?php
            printf(
                /* translators: %s: WordPress name. */
                esc_html__('Proudly powered by %s.', 'opencode-theme-example'),
                '<a href="' . esc_url(__('https://wordpress.org/', 'opencode-theme-example')) . '">WordPress</a>'
            );
            ?>
        </div>
    </div>
</footer>
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
