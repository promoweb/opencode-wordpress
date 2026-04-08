<?php
/**
 * The footer for the theme
 *
 * @package Opencode_Theme_Example
 */
?>

    <footer id="colophon" class="site-footer">
        <div class="site-info">
            <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'opencode-theme-example' ) ); ?>">
                <?php printf( esc_html__( 'Proudly powered by %s', 'opencode-theme-example' ), 'WordPress' ); ?>
            </a>
            <span class="sep"> | </span>
            <?php printf( esc_html__( 'Theme: %1$s by %2$s.', 'opencode-theme-example' ), 'opencode-theme-example', '<a href="https://example.com">OpenCode</a>' ); ?>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>

</body>
</html>