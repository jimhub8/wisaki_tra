<?php
/**
 * MY ACCOUNT TEMPLATE MENU ITEM
 *
 * @since 2.0.0
 */
if ( ! defined( 'YITH_WCMAP' ) ) {
    exit;
} // Exit if accessed directly

// if array implode
is_array( $classes ) && $classes = implode( ' ', $classes );
$target = ( isset( $options['target_blank'] ) && $options['target_blank'] ) ? 'target="_blank"' : '';
?>

<li class="<?php echo $classes ?>">
    <a class="<?php echo apply_filters( 'yith_wcmap_endpoint_anchor_tag_class', 'yith-' . $endpoint ); ?>" href="<?php echo esc_url( $url ) ?>" title="<?php echo esc_attr( $options['label'] ) ?>" <?php echo $target ?>>
        <?php if( ! empty( $options['icon'] ) ) :
            // prevent double fa-
            $icon = strpos( $options['icon'], 'fa-' ) === false ? 'fa-' . $options['icon'] : $options['icon']; ?>
            <i class="fa <?php echo $icon; ?>"></i>
        <?php endif; ?>
        <span><?php echo $options['label'] ?></span>
    </a>
</li>