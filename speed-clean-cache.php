<?php
/*
Plugin Name: Speed Clean Cache
Description: A simple plugin to clean cache and optimize the website.
Version: 1.0
Author: Hamid Ezzaki
Author URI: https://siteweb.es
License: GPL2
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Enqueue Admin Scripts and Styles
function scc_enqueue_admin_scripts($hook) {
    if ('toplevel_page_speed-clean-cache' !== $hook) {
        return;
    }
    wp_enqueue_style('scc-admin-style', plugin_dir_url(__FILE__) . 'css/admin-style.css');
    wp_enqueue_script('scc-admin-script', plugin_dir_url(__FILE__) . 'js/admin-script.js', array('jquery'), false, true);
}
add_action('admin_enqueue_scripts', 'scc_enqueue_admin_scripts');

// Add Admin Menu
function scc_add_admin_menu() {
    add_menu_page(
        'Speed Clean Cache',        // Page Title
        'Speed Clean Cache',        // Menu Title
        'manage_options',           // Capability
        'speed-clean-cache',        // Menu Slug
        'scc_admin_page',           // Function to display the admin page
        'dashicons-trash',          // Icon
        30                          // Position
    );
}
add_action('admin_menu', 'scc_add_admin_menu');

// Admin Page HTML
function scc_admin_page() {
    ?>
    <div class="wrap">
        <h1>Speed Clean Cache</h1>
        <p>Click the button below to clean the cache and optimize your website.</p>
        
        <form method="post" action="">
            <?php wp_nonce_field('scc_clean_cache', 'scc_nonce'); ?>
            <input type="submit" name="scc_clean_cache" class="button button-primary" value="Clean Cache & Optimize">
        </form>

        <?php
        if (isset($_POST['scc_clean_cache']) && check_admin_referer('scc_clean_cache', 'scc_nonce')) {
            scc_clean_cache_function();
        }
        ?>
    </div>
    <?php
}

// Cache Cleaning and Optimization Function
function scc_clean_cache_function() {
    // Clear the WordPress Cache (for some caching plugins)
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }

    // Clear object cache (Redis or Memcached)
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
    }

    // Clear transients
    global $wpdb;
    $wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '\_transient\_%'");

    // Optimize the database (clean up revisions, etc.)
    if (function_exists('wp_optimize_database')) {
        wp_optimize_database();
    }

    // Optional: Clear specific plugin caches (if needed for certain plugins)

    echo '<div class="updated"><p>Cache cleared and website optimized!</p></div>';
}
?>
