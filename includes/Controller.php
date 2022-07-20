<?php

namespace WPRestApiAggregator;

use WP_REST_Controller;
use WP_REST_Server;

/**
 * Aggregator REST Controller.
 */
class Controller extends WP_REST_Controller
{
    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'wp-rest-api-aggregator/v1';

    /**
     * Route name.
     *
     * @var string
     */
    protected $base = 'aggregate';

    /**
     * Class constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->register_routes();
    }

    /**
     * Register REST routes.
     *
     * @return void
     */
    public function register_routes()
    {
        register_rest_route(
            $this->namespace,
            '/' . $this->base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    'permission_callback' => '__return_true',
                    'args'                => [
                        'requests' => [
                            'required'    => true,
                            'description' => esc_html__( 'List of WP REST API requests.', 'wp-rest-api-aggregator' ),
                            'type'        => 'array',
                            'items'       => [
                                'description' => esc_html__( 'WP REST API requests.', 'wp-rest-api-aggregator' ),
                                'type'        => 'string',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Get debug log.
     *
     * @param \WP_REST_Request $request Full details about the request.
     *
     * @return \WP_REST_Response|\WP_Error Response object on success, or WP_Error object on failure.
     */
    public function get_items( $request )
    {
        /**
         * The custom REST server.
         *
         * @var \WPRestApiAggregator\Server
         */
        $wp_rest_server = rest_get_server();

        $responses = [];

        foreach ( $request['requests'] as $rest_request ) {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            $tmp_get      = $_GET;
            $rest_request = '/' . ltrim( $rest_request, '//' );
            $parsed_url   = wp_parse_url( $rest_request );
            $path         = $parsed_url['path'];

            if ( ! empty( $parsed_url['query'] ) ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                parse_str( $parsed_url['query'], $_GET );
            }

            $wp_rest_server->set_is_part_of_aggregator();

            ob_start();
            $wp_rest_server->serve_request( $path );
            $responses[ $rest_request ] = json_decode( ob_get_clean(), true );

            $wp_rest_server->reset_is_part_of_aggregator();

            $_GET = $tmp_get;
        }

        return rest_ensure_response( $responses );
    }
}
