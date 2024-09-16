<?php
/*
Module Name: Disable Author Pages
Module URI: https://www.littlebizzy.com/plugins/disable-author-pages
Description: Disables author pages and links
Version: 2.0.2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Disable author pages by returning a 404 status and letting WordPress handle the template
add_action('template_redirect', function () {
    if (!is_admin() && (is_author() || (isset($_GET['author']) && $_GET['author']))) {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        exit;
    }
}, 1);

// Disable author links
add_filter('author_link', '__return_false', 99);
add_filter('the_author_posts_link', function () {
    return '';
}, 99);

// Remove only author-specific feed link from the head
remove_action('wp_head', 'wp_author_feed_link');

// Remove all filters from 'author_link' to prevent other plugins or themes from modifying author URLs.
remove_all_filters('author_link');

// Remove the users (author) sitemap provider from WordPress Core XML Sitemaps
add_filter('wp_sitemaps_add_provider', function ($provider, $name) {
    if ('users' === $name) {
        return false; // Prevent user sitemaps from being generated by WordPress Core
    }
    return $provider;
}, 10, 2);

// Block direct access to author.php template and load WordPress's 404 page
add_action('template_include', function ($template) {
    if (basename($template) == 'author.php') {
        global $wp_query;
        $wp_query->set_404();
        status_header(404);
        nocache_headers();
        return get_query_template('404');
    }
    return $template;
});

// Remove author links from REST API responses, but keep other author details
add_filter('rest_prepare_post', function ($response, $post, $request) {
    $data = $response->get_data();
    if (isset($data['_links']['author'])) {
        unset($data['_links']['author']);
    }
    $response->set_data($data);
    return $response;
}, 10, 3);

// Ref: ChatGPT