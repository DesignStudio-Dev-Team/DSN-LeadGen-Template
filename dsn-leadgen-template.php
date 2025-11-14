<?php
/*
Plugin Name: DSN LeadGen Template
Plugin URI:  https://designstudio.com/
Description: Opinionated two-column lead gen page template with Gravity Forms style overrides (vanilla CSS & JS).
Version: 0.1.0
Author: DesignStudio Network, Inc.
Text Domain: dsn-leadgen-template
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DSN_LG_DIR', plugin_dir_path( __FILE__ ) );
define( 'DSN_LG_URL', plugin_dir_url( __FILE__ ) );

// Enqueue front-end assets late so they can override theme styles (priority 999)
add_action( 'wp_enqueue_scripts', 'dsn_lg_enqueue_assets', 999 );
function dsn_lg_enqueue_assets() {
    wp_enqueue_style( 'dsn-lgt-style', DSN_LG_URL . 'assets/css/dsn-leadgen-template.css', array(), '0.1.0' );
    wp_enqueue_script( 'dsn-lgt-script', DSN_LG_URL . 'assets/js/dsn-leadgen-template.js', array(), '0.1.0', true );
}

// Register page template
add_filter( 'theme_page_templates', 'dsn_lg_register_template' );
function dsn_lg_register_template( $templates ) {
    $templates['templates/template-dsn-leadgen.php'] = __( 'DSN Lead Generation Page', 'dsn-leadgen-template' );
    return $templates;
}

add_filter( 'template_include', 'dsn_lg_template_include' );
function dsn_lg_template_include( $template ) {
    if ( is_page() ) {
        $post_id = get_the_ID();
        if ( $post_id ) {
            $selected = get_post_meta( $post_id, '_wp_page_template', true );
            if ( 'templates/template-dsn-leadgen.php' === $selected ) {
                $plugin_template = DSN_LG_DIR . 'templates/template-dsn-leadgen.php';
                if ( file_exists( $plugin_template ) ) {
                    return $plugin_template;
                }
            }
        }
    }
    return $template;
}

// Add meta box for template-specific content (only visible when template is selected)
add_action( 'add_meta_boxes', 'dsn_lg_register_meta_box' );
function dsn_lg_register_meta_box() {
    add_meta_box(
        'dsn_lg_content',
        __( 'DSN LeadGen Page Content', 'dsn-leadgen-template' ),
        'dsn_lg_meta_box_callback',
        'page',
        'normal',
        'high'
    );
}

/**
 * Render the DSN meta box UI. Uses the WP media library for choosing logo/image.
 */
function dsn_lg_meta_box_callback( $post ) {
    // Only show inputs when the DSN template is selected.
    $template = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( 'templates/template-dsn-leadgen.php' !== $template ) {
        echo '<p>' . esc_html__( 'Select the "DSN Lead Generation Page" template to edit DSN-specific content.', 'dsn-leadgen-template' ) . '</p>';
        return;
    }

    // Nonce
    wp_nonce_field( 'dsn_lg_save_meta', 'dsn_lg_meta_nonce' );

    $main_content = get_post_meta( $post->ID, '_dsn_main_content', true );
    $logo_id      = get_post_meta( $post->ID, '_dsn_logo_id', true );
    $logo_url     = get_post_meta( $post->ID, '_dsn_logo_url', true );
    $media_id     = get_post_meta( $post->ID, '_dsn_media_id', true );
    $media_url    = get_post_meta( $post->ID, '_dsn_media_url', true );
    $media_poster = get_post_meta( $post->ID, '_dsn_media_poster', true );
    $image_id     = get_post_meta( $post->ID, '_dsn_image_id', true );
    $image_url    = get_post_meta( $post->ID, '_dsn_image_url', true );
    $right_intro  = get_post_meta( $post->ID, '_dsn_right_content', true );
    $media_type   = get_post_meta( $post->ID, '_dsn_media_type', true );
    if ( empty( $media_type ) ) { $media_type = 'image'; }

    // Main content WYSIWYG
    echo '<p><strong>' . esc_html__( 'Main content (left column)', 'dsn-leadgen-template' ) . '</strong></p>';
    wp_editor( $main_content, 'dsn_main_content', array( 'textarea_name' => 'dsn_main_content', 'media_buttons' => false ) );

    echo '<p><strong>' . esc_html__( 'Right column content (WYSIWYG)', 'dsn-leadgen-template' ) . '</strong></p>';
    wp_editor( $right_intro, 'dsn_right_content', array( 'textarea_name' => 'dsn_right_content', 'media_buttons' => false ) );

    // Logo media uploader
    echo '<p><strong>' . esc_html__( 'Logo (left column)', 'dsn-leadgen-template' ) . '</strong></p>';
    echo '<div class="dsn-media-row">';
    echo '<input type="hidden" name="dsn_logo_id" id="dsn_logo_id" value="' . esc_attr( $logo_id ) . '" />';
    echo '<input type="hidden" name="dsn_logo_url" id="dsn_logo_url" value="' . esc_attr( $logo_url ) . '" />';
    echo '<div class="dsn-media-preview"><img id="dsn_logo_preview" src="' . esc_attr( $logo_url ) . '" style="max-width:200px;max-height:80px;'. ( $logo_url ? '' : 'display:none;' ) .'" /></div>';
    echo '<p><button type="button" class="button dsn-upload-button" data-target="logo">Select Logo</button> <button type="button" class="button dsn-remove-button" data-target="logo"' . ( $logo_url ? '' : ' style="display:none;"' ) . '>Remove</button></p>';
    echo '</div>';

        // Media type selection (image or video)
    echo '<p><strong>' . esc_html__( 'Left column media type', 'dsn-leadgen-template' ) . '</strong></p>';
    echo '<p>';
    echo '<label><input type="radio" name="dsn_media_type" value="image" ' . checked( $media_type, 'image', false ) . ' /> ' . esc_html__( 'Image (default)', 'dsn-leadgen-template' ) . '</label><br/>';
    echo '<label><input type="radio" name="dsn_media_type" value="video" ' . checked( $media_type, 'video', false ) . ' /> ' . esc_html__( 'Video', 'dsn-leadgen-template' ) . '</label>';
    echo '</p>';
    
    // Left column image uploader (visible when media_type === 'image')
    echo '<div class="dsn-media-section dsn-media-section--image"' . ( $media_type === 'image' ? '' : ' style="display:none;"' ) . '>';
    echo '<p><strong>' . esc_html__( 'Image (left column)', 'dsn-leadgen-template' ) . '</strong></p>';
    echo '<div class="dsn-media-row">';
    echo '<input type="hidden" name="dsn_image_id" id="dsn_image_id" value="' . esc_attr( $image_id ) . '" />';
    echo '<input type="hidden" name="dsn_image_url" id="dsn_image_url" value="' . esc_attr( $image_url ) . '" />';
    echo '<div class="dsn-media-preview"><img id="dsn_image_preview" src="' . esc_attr( $image_url ) . '" style="max-width:100%;height:120px;object-fit:cover;' . ( $image_url ? '' : 'display:none;' ) . '" /></div>';
    echo '<p><button type="button" class="button dsn-upload-button" data-target="image">Select Image</button> <button type="button" class="button dsn-remove-button" data-target="image"' . ( $image_url ? '' : ' style="display:none;"' ) . '>Remove</button></p>';
    echo '</div>';
    echo '</div>';

    // Video fields (visible when media_type === 'video')
    echo '<div class="dsn-media-section dsn-media-section--video"' . ( $media_type === 'video' ? '' : ' style="display:none;"' ) . '>';
    echo '<p><strong>' . esc_html__( 'Optional Video URL', 'dsn-leadgen-template' ) . '</strong></p>';
    echo '<input style="width:100%;" type="text" name="dsn_media_url" id="dsn_media_url" value="' . esc_attr( $media_url ) . '" />';
    echo '<p><strong>' . esc_html__( 'Video Poster (optional)', 'dsn-leadgen-template' ) . '</strong></p>';
    echo '<div class="dsn-media-row">';
    echo '<input type="hidden" name="dsn_media_id" id="dsn_media_id" value="' . esc_attr( $media_id ) . '" />';
    echo '<input type="hidden" name="dsn_media_poster" id="dsn_media_poster" value="' . esc_attr( $media_poster ) . '" />';
    echo '<div class="dsn-media-preview"><img id="dsn_media_preview" src="' . esc_attr( $media_poster ) . '" style="max-width:100%;height:120px;object-fit:cover;' . ( $media_poster ? '' : 'display:none;' ) . '" /></div>';
    echo '<p><button type="button" class="button dsn-upload-button" data-target="media">Select Poster</button> <button type="button" class="button dsn-remove-button" data-target="media"' . ( $media_poster ? '' : ' style="display:none;"' ) . '>Remove</button></p>';
    echo '</div>';
    echo '</div>';
}

add_action( 'save_post_page', 'dsn_lg_save_meta' );
function dsn_lg_save_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! isset( $_POST['dsn_lg_meta_nonce'] ) || ! wp_verify_nonce( $_POST['dsn_lg_meta_nonce'], 'dsn_lg_save_meta' ) ) {
        return;
    }
    if ( isset( $_POST['dsn_main_content'] ) ) {
        update_post_meta( $post_id, '_dsn_main_content', wp_kses_post( $_POST['dsn_main_content'] ) );
    }
    if ( isset( $_POST['dsn_right_content'] ) ) {
        update_post_meta( $post_id, '_dsn_right_content', wp_kses_post( $_POST['dsn_right_content'] ) );
    }
    // Media type (image or video)
    if ( isset( $_POST['dsn_media_type'] ) ) {
        update_post_meta( $post_id, '_dsn_media_type', sanitize_text_field( $_POST['dsn_media_type'] ) );
    }

    // Logo (ID + URL)
    if ( isset( $_POST['dsn_logo_id'] ) ) {
        update_post_meta( $post_id, '_dsn_logo_id', absint( $_POST['dsn_logo_id'] ) );
    }
    if ( isset( $_POST['dsn_logo_url'] ) ) {
        update_post_meta( $post_id, '_dsn_logo_url', esc_url_raw( $_POST['dsn_logo_url'] ) );
    }

    // Image (ID + URL)
    if ( isset( $_POST['dsn_image_id'] ) ) {
        update_post_meta( $post_id, '_dsn_image_id', absint( $_POST['dsn_image_id'] ) );
    }
    if ( isset( $_POST['dsn_image_url'] ) ) {
        update_post_meta( $post_id, '_dsn_image_url', esc_url_raw( $_POST['dsn_image_url'] ) );
    }

    // Video URL and poster
    if ( isset( $_POST['dsn_media_url'] ) ) {
        update_post_meta( $post_id, '_dsn_media_url', esc_url_raw( $_POST['dsn_media_url'] ) );
    }
    if ( isset( $_POST['dsn_media_id'] ) ) {
        update_post_meta( $post_id, '_dsn_media_id', absint( $_POST['dsn_media_id'] ) );
    }
    if ( isset( $_POST['dsn_media_poster'] ) ) {
        update_post_meta( $post_id, '_dsn_media_poster', esc_url_raw( $_POST['dsn_media_poster'] ) );
    }
}

/**
 * Enqueue admin JS/CSS for the meta box (media uploader support).
 */
add_action( 'admin_enqueue_scripts', 'dsn_lg_admin_assets' );
function dsn_lg_admin_assets( $hook ) {
    // Only load on page edit screens
    if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
        return;
    }
    $screen = get_current_screen();
    if ( ! $screen || 'page' !== $screen->post_type ) {
        return;
    }

    wp_enqueue_media();
    wp_enqueue_script( 'dsn-lgt-admin', DSN_LG_URL . 'assets/js/admin-dsn-leadgen.js', array( 'jquery' ), '0.1.0', true );
    wp_enqueue_style( 'dsn-lgt-admin-css', DSN_LG_URL . 'assets/css/admin-dsn-leadgen.css', array(), '0.1.0' );
    wp_localize_script( 'dsn-lgt-admin', 'dsnLgAdmin', array( 'nonce' => wp_create_nonce( 'dsn_lg_meta' ), 'pluginUrl' => DSN_LG_URL ) );
}
