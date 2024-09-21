<?php
/*
Module Name: Disable Feeds
Module URI: https://www.littlebizzy.com/plugins/disable-feeds
Description: Disables RSS and 301s to parent
Version: 1.0.0
*/

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Disable all RSS Feeds and redirect to the parent URL
function disable_all_feeds() {
    if ( is_feed() ) {
        // Get current URL and remove /feed
        $current_url = home_url( add_query_arg( null, null ) );
        $parent_url = preg_replace( '#(/feed.*)#', '', $current_url );

        // Safe redirect to the parent URL
        wp_safe_redirect( esc_url_raw( $parent_url ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'disable_all_feeds', 20 );

// Disable built-in feed types (RSS, RDF, Atom, and Comments)
function disable_default_feeds() {
    remove_action( 'do_feed_rdf', 'do_feed_rdf', 20 );
    remove_action( 'do_feed_rss', 'do_feed_rss', 20 );
    remove_action( 'do_feed_rss2', 'do_feed_rss2', 20 );
    remove_action( 'do_feed_atom', 'do_feed_atom', 20 );
    remove_action( 'do_feed_rss2_comments', 'do_feed_rss2_comments', 20 );
    remove_action( 'do_feed_atom_comments', 'do_feed_atom_comments', 20 );
}
add_action( 'init', 'disable_default_feeds', 20 );

// Disable feeds for custom post types
function disable_custom_post_type_feeds() {
    foreach ( get_post_types( array( 'public' => true ) ) as $post_type ) {
        remove_action( "do_feed_{$post_type}", 'do_feed_rss2', 20 );
    }
}
add_action( 'init', 'disable_custom_post_type_feeds', 20 );

// Remove feed links from <head>
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );

// Disable comment feeds
add_filter( 'feed_links_show_comments_feed', '__return_false' );

// Ref: ChatGPT
