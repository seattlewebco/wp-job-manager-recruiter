<?php
/**
 * Main WP_Job_Manager_JobAdder class file
 * 
 * @package WP Job Manager - JobAdder Integration
 */


namespace SeattleWebCo\WPJobManager\Recruiter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


require_once 'includes/interface-adapter.php';
require_once 'includes/class-client.php';
require_once 'includes/class-applications.php';
require_once 'includes/class-webhooks.php';
require_once 'includes/class-log.php';
require_once 'includes/class-exception.php';
require_once 'includes/class-cron.php';


class Recruiter {

    /**
     * Applications handling class
     *
     * @var Applications
     */
    public $applications;


    /**
     * Webhooks handling class
     *
     * @var Webhooks
     */
    public $webhooks;


    /**
     * Logger class
     *
     * @var Log
     */
    public $log;


    /**
     * Cron jobs class
     *
     * @var Cron
     */
    public $cron;


    /**
     * Provider client
     *
     * @var Client
     */
    public $client;

    
    /**
     * Insures that only one instance of WP_Job_Manager_JobAdder exists in memory at any one time.
     * 
     * @return Recruiter The one true instance of Recruiter
     */
    public function __construct( Interface_Adapter $adapter ) {
        $this->client         = new Client( $adapter );
        $this->applications   = new Applications;
        $this->webhooks       = new Webhooks;
        $this->log            = new Log;
        $this->cron           = new Cron;

        do_action_ref_array( 'wp_job_manager_recruiter_loaded', $this ); 
    
        return $this;
    }
}