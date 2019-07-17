<?php
/**
 * Webhooks
 * 
 * @package WP Job Manager - JobAdder Integration
 */

 
namespace WPJobManagerRecruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Webhooks {

    /**
     * Enabled webhook events
     * 
     * @var array
     */
    public $events = array( 'job_status_changed', 'jobad_posted', 'jobad_expired' );


    /**
     * List of enabled applicable webhook IDs
     *
     * @var array
     */
    public $webhook_ids = array();


    /**
     * Setup
     */
    public function __construct() {
        add_action( 'init', array( $this, 'webhooks_listener' ) );
    }


    /**
     * Listens for incoming event data from JobAdder
     *
     * @return void
     */
    public function webhooks_listener() {
        if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset( $_GET['job_manager_webhook'] ) && $_GET['job_manager_webhook'] == 'jobadder' ) {
            // 
            exit;
        }
    }


    /**
     * Returns array of required webhook events
     *
     * @return array
     */
    public function get_events() {
        return apply_filters( 'job_manager_jobadder_webhooks_events', $this->events );
    }


    /**
     * Returns array of enabled webhook events
     * 
     * @return array
     */
    public function get_enabled_events() {
        $webhooks = WP_Job_Manager_JobAdder()->client->adapter()->get_webhooks( array(), 'Enabled' );

        $events = array();

        if ( ! is_wp_error( $webhooks ) ) {

            foreach ( $webhooks->items as $webhook ) {
                /**
                 * Validate the webhook is for this website and plugin
                 */
                if ( basename( parse_url( $webhook->url, PHP_URL_HOST ) ) !== basename( home_url() ) || strpos( parse_url( $webhook->url, PHP_URL_QUERY ), 'job_manager_webhook=jobadder' ) === false ) {
                    continue;
                }

                $events = array_merge( $events, (array) $webhook->events );

                $this->webhook_ids[] = $webhook->webhookId;
            }
        }

        return array_unique( $events );
    }


    /**
     * Sets up the webhook
     *
     * @return mixed
     */
    public function setup() {
        return WP_Job_Manager_JobAdder()->client->adapter()->post_webhook( 'wp_job_manager_jobadder', $this->get_events(), 'Enabled' );
    }


    /**
     * If there are duplicate webhooks sending multiple of the same request, fix it
     * 
     * @return boolean
     */
    public function reset() {
        if ( sizeof( $this->webhook_ids ) > 1 ) {
            foreach ( $this->webhook_ids as $webhook_id ) {
                WP_Job_Manager_JobAdder()->client->adapter()->delete_webhook( $webhook_id );
            }

            $this->setup();

            return true;
        }

        return false;
    }
}