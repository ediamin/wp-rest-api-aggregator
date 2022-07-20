<?php
/**
 * Plugin Name: WP REST API Aggregator
 * Description: Get multiple WordPress REST API response in a single request.
 * Version: 1.0.0
 * Author: Edi Amin
 * Author URI: https://github.com/ediamin
 * Text Domain: wp-rest-api-aggregator
 * License: GPLv2 or later
 * Domain Path: /languages/
 */

// Plugin constants.
define( 'WP_REST_API_AGGREGATOR_PLUGIN_FILE', __FILE__ );
define( 'WP_REST_API_AGGREGATOR_PLUGIN_ROOT', dirname( WP_REST_API_AGGREGATOR_PLUGIN_FILE ) );

// Use the custom REST Server.
add_filter(
    'wp_rest_server_class',
    function () {
        require_once WP_REST_API_AGGREGATOR_PLUGIN_ROOT . '/includes/Server.php';
        return \WPRestApiAggregator\Server::class;
    }
);

// Add custom REST API endpoint.
add_action(
    'rest_api_init',
    function () {
        require_once WP_REST_API_AGGREGATOR_PLUGIN_ROOT . '/includes/Controller.php';
        new \WPRestApiAggregator\Controller();
    }
);
