<?php
/**
 * Template Name: DSN Lead Generation Page
 *
 * This template renders the two-column lead gen layout. Content editors should
 * place their Gravity Forms shortcode in the main page content area (right column).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

global $post;


$topContent = get_the_content($post->ID);
$logo_url     = get_post_meta( $post->ID, 'dsn_lgp_logo_url', true );
$image_url    = get_post_meta( $post->ID, 'dsn_lgp_image_url', true );
$main_content = get_post_meta( $post->ID, 'dsn_lgp_main_content', true );
$right_intro  = get_post_meta( $post->ID, 'dsn_lgp_right_content', true );
$media_type   = get_post_meta( $post->ID, 'dsn_lgp_media_type', true );
if ( empty( $media_type ) ) { $media_type = 'image'; }
$media_url    = get_post_meta( $post->ID, 'dsn_lgp_media_url', true );
$media_poster = get_post_meta( $post->ID, 'dsn_lgp_media_poster', true );
$padding_top    = get_post_meta( $post->ID, 'dsn_lgp_padding_top', true );
$padding_right  = get_post_meta( $post->ID, 'dsn_lgp_padding_right', true );
$padding_bottom = get_post_meta( $post->ID, 'dsn_lgp_padding_bottom', true );
$padding_left   = get_post_meta( $post->ID, 'dsn_lgp_padding_left', true );

$wrapper_style = '';
if ( $padding_top || $padding_right || $padding_bottom || $padding_left ) {
    $wrapper_style = 'style="';
    
    // Helper to ensure unit
    $ensure_unit = function($val) {
        $val = trim($val);
        return is_numeric($val) ? $val . 'px' : $val;
    };

    if ( $padding_top ) $wrapper_style .= 'padding-top:' . esc_attr( $ensure_unit($padding_top) ) . ';';
    if ( $padding_right ) $wrapper_style .= 'padding-right:' . esc_attr( $ensure_unit($padding_right) ) . ';';
    if ( $padding_bottom ) $wrapper_style .= 'padding-bottom:' . esc_attr( $ensure_unit($padding_bottom) ) . ';';
    if ( $padding_left ) $wrapper_style .= 'padding-left:' . esc_attr( $ensure_unit($padding_left) ) . ';';
    $wrapper_style .= '"';
}

?>

<div class="dsn-leadgen-top">
    <?php if ( $topContent ) : ?>
        <?php echo apply_filters( 'the_content', $topContent ); ?>
    <?php endif; ?>
</div>

<div id="dsn-leadgen-wrapper" class="dsn-leadgen-wrap" <?php echo $wrapper_style; ?>>
    <div class="dsn-left">
        <?php if ( $main_content ) : ?>
            <div class="dsn-left-content"> <?php echo apply_filters( 'the_content', $main_content ); ?> </div>
        <?php endif; ?>

        <div class="dsn-logo-lp-wrapper">
        <?php if ( $logo_url ) : ?>
            <div class="dsn-logo-lp"><img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo"/></div>
        <?php endif; ?>

        <?php if ( 'video' === $media_type && $media_url ) : ?>
            <div class="dsn-image dsn-image--video">
                <video muted autoplay loop preload="metadata" <?php echo $media_poster ? 'poster="' . esc_attr( $media_poster ) . '"' : ''; ?> style="width:100%;height:758px;object-fit:cover;">
                    <source src="<?php echo esc_url( $media_url ); ?>" />
                    <?php esc_html_e( 'Your browser does not support the video tag.', 'dsn-leadgen-template' ); ?>
                </video>
            </div>
        <?php elseif ( $image_url ) : ?>
            <div class="dsn-image" style="background-image: url('<?php echo esc_url( $image_url ); ?>')"></div>
        <?php else: ?>
            <div class="dsn-image dsn-image--placeholder"></div>
        <?php endif; ?>
        </div>
      
    </div>

    <div class="dsn-right">
        <div class="dsn-right-inner">
            <?php if ( $right_intro ) : ?>
                <div class="dsn-right-intro"><?php echo apply_filters( 'the_content', $right_intro ); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
get_footer();
