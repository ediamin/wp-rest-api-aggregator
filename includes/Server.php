<?php

namespace WPRestApiAggregator;

use WP_REST_Server;

/**
 * Custom WordPress REST API server.
 */
class Server extends WP_REST_Server
{
    /**
     * Whether the current processing path is coming from the aggregator REST controller or not.
     *
     * @var bool
     */
    protected $is_part_of_aggregator = false;

    /**
     * Store the header instead of send immediately, so we can send them in REST response.
     *
     * @var array
     */
    protected $header_stores = [];

    /**
     * Instantiates the REST server.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        add_filter( 'rest_pre_echo_response', [ $this, 'aggregated_response' ], 10, 2 );
    }

    /**
     * Sends an HTTP header.
     *
     * @param string $key   Header key.
     * @param string $value Header value.
     *
     * @return void
     */
    public function send_header( $key, $value )
    {
        if ( $this->is_part_of_aggregator ) {
            $this->header_stores[ $key ] = $value;
            return;
        }

        parent::send_header( $key, $value );
    }

    /**
     * Removes an HTTP header from the current response.
     *
     * @param string $key Header key.
     *
     * @return void
     */
    public function remove_header( $key )
    {
        if ( $this->is_part_of_aggregator ) {
            unset( $this->header_stores[ $key ] );
            return;
        }

        header_remove( $key );
    }

    /**
     * Set the current processing path as a part of the aggregator REST controller.
     *
     * @return void
     */
    public function set_is_part_of_aggregator()
    {
        $this->is_part_of_aggregator = true;
        $this->header_stores         = [];
    }

    /**
     * Reset the current processing path as a part of the aggregator REST controller.
     *
     * @return void
     */
    public function reset_is_part_of_aggregator()
    {
        $this->is_part_of_aggregator = false;
        $this->header_stores         = [];
    }

    /**
     * Filter the aggregator REST response and add the header data.
     *
     * @param array          $result         Response data to send to the client.
     * @param WP_REST_Server $wp_rest_server Server instance.
     *
     * @return array $result Response data to send to the client.
     */
    public function aggregated_response( $result, $wp_rest_server )
    {
        if ( $wp_rest_server->is_part_of_aggregator ) {
            $result = [
                'data' => $result,
            ];

            if ( ! empty( $wp_rest_server->header_stores ) ) {
                $result['_headers'] = $wp_rest_server->header_stores;
            }
        }

        return $result;
    }
}
