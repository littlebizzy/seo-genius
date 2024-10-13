<?php
/*
Module Name: Enable Subtitles
Module URI: https://www.littlebizzy.com/plugins/enable-subtitles
Description: Creates new the_subtitle function
Version: 2.0.0
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Retrieve the subtitle for a post or page
function get_the_subtitle( $post_id = null ) {
    $post_id = $post_id ? $post_id : get_the_ID();
    $subtitle = get_post_meta( $post_id, '_subtitle', true ); // Retrieve subtitle

    // Return the subtitle or null if it doesn't exist
    return apply_filters( 'enable_subtitles_output', wp_kses_post( $subtitle ), $post_id ); // Sanitize during output
}

// Display the subtitle for a post or page
function the_subtitle() {
    global $post;

    if ( isset( $post->ID ) ) {
        $subtitle = get_the_subtitle( $post->ID );

        // Only display the subtitle if it exists
        if ( !empty( $subtitle ) ) {
            // Allow modification of the heading level
            $heading_tag = apply_filters( 'subtitle_heading_tag', 'h2' ); // Default to h2
            echo '<' . esc_html( $heading_tag ) . ' class="subtitle">' . $subtitle . '</' . esc_html( $heading_tag ) . '>';
        }
    }
}

// Register the shortcode for displaying the subtitle
add_shortcode( 'subtitle', 'the_subtitle' );

// Enable subtitle support for all custom post types
function enable_subtitle_support() {
    // Get all public post types
    $post_types = get_post_types(['public' => true], 'names');

    // Allow customization of post types that should support subtitles
    $post_types_with_subtitles = apply_filters('enable_subtitles_post_types', $post_types);

    // Loop through the post types and ensure they can support subtitles
    foreach ($post_types_with_subtitles as $post_type) {
        // Since there’s no direct post type support for 'subtitle', you can handle it via post meta
        // Here you could add any additional logic if needed for specific post types in the future
    }
}

// Hook into WordPress
add_action('init', 'enable_subtitle_support');

// Admin functions
if ( is_admin() ) {
    // Add the subtitle input field after the post title
    function add_subtitle_field() {
        add_action( 'edit_form_before_permalink', 'render_subtitle_field' );
    }

    // Render the subtitle field
    function render_subtitle_field( $post ) {
        // Security nonce field for form submission verification
        wp_nonce_field( 'subtitle_nonce_action', 'subtitle_nonce' );

        // Retrieve the existing subtitle and sanitize during output
        $subtitle = esc_html( get_post_meta( $post->ID, '_subtitle', true ) );

        // Output the HTML for the subtitle input field
        echo '
        <div id="subtitlewrap">
            <input type="text" name="post_subtitle" size="30" value="' . esc_attr( $subtitle ) . '" id="subtitle" placeholder="' . esc_attr__( 'Add subtitle', 'enable-subtitles' ) . '" spellcheck="true" autocomplete="off" class="widefat" style="font-size: 18px;" />
        </div>';
    }

    // Save the subtitle meta
    function save_subtitle( $post_id ) {
        // Check nonce for security
        if ( ! isset( $_POST['subtitle_nonce'] ) || ! wp_verify_nonce( $_POST['subtitle_nonce'], 'subtitle_nonce_action' ) ) {
            return;
        }

        // Check user permissions
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save or update the subtitle
        if ( isset( $_POST['post_subtitle'] ) ) {
            update_post_meta( $post_id, '_subtitle', sanitize_text_field( $_POST['post_subtitle'] ) );
        }
    }

    add_action( 'add_meta_boxes', 'add_subtitle_field' );
    add_action( 'save_post', 'save_subtitle' );
}

// Ref: ChatGPT
